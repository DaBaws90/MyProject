<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Familium;
use DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categories = Familium::orderBy('familia', 'asc')->get();
        return response()->json(['categories' => $categories], 200);
    }

    /**
     * Display the specified resources
     * 
     * @param \Illuminate\Http\Request  $request
     * @return JSON response
     */
    public function references(Request $request) {
        $products = array();
        $totalPCB = 0.0; $totalPCC = 0.0; $totalDifference = 0.0; $totalPercentage = 0.0;

        if($request->references != null){
            $productsRefs = $this->explodeString($request->references);

            foreach ($productsRefs as $ref) {
                $existsOrNot = DB::select("SELECT pcbox.codigo, pcbox.nombre, pcbox.precio, pcbox.enlace as enlace, pccomponentes.referencia_fabricante,
                    pccomponentes.precio as precioPccomp, pccomponentes.enlace as enlacePccomp FROM pcbox,pccomponentes WHERE pcbox.codigo LIKE '".$this->trimAndFormat($ref)."'
                    AND pcbox.referencia_fabricante = pccomponentes.referencia_fabricante");

                if($existsOrNot) {
                    array_push($products, $existsOrNot[0]);
                }
            }

            foreach ($products as $index => $product) {
                # code...
                $product->nombre = strtoupper($product->nombre);
                if($product->precio !=0){
                    $products[$index]->difference = round($product->precioPccomp - $product->precio, 2, PHP_ROUND_HALF_UP);
                    $products[$index]->percentage = round(($product->difference / $product->precio) * 100, 2, PHP_ROUND_HALF_UP);
                    $totalPCB += $product->precio;
                    $totalPCC += $product->precioPccomp;
                }
                else{
                    $products[$index]->precio = "Consultar";
                    $products[$index]->difference = null;
                    $products[$index]->percentage = null;
                }
                
            }

            $totalDifference = $totalPCC - $totalPCB;
            if($totalPCB != 0){
                $totalPercentage = round(($totalDifference / $totalPCB) * 100, 2);
            }
            else{
                $totalPercentage = null;
            }
        }
        else {
            return response()->json(['error' => 'The search input was empty. Please, type something in order to search products']);
        }

        if(count($products) > 0) {
            $results = array();
            $results['products'] = $products;
            $results['totalPCB'] = $totalPCB;
            $results['totalPCC'] = $totalPCC;
            $results['totalDifference'] = $totalDifference;
            $results['totalPercentage'] = $totalPercentage;
            return response()->json(['productsArray' => $results], 200);
        }
        
        return response()->json(['error' => 'No products were found. Please, try again with different search parameters']);
    }

    /**
     * Display the specified resources.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function categories(Request $request){
        
        $categoryQuery = "";

        if($request->category != 0){
            $category = Familium::find($request->category);
            $categoryQuery = " pccomponentes.familia = '".$category->familia."' AND";
        }

        // Evaluating if keywords have been entered in search field
        $keywordQuery = "";

        if($request->keyword != null){
            if($request->keyword1 != null){
                $keywordQuery = " AND (";
            }
            else{
                $keywordQuery = " AND ";
            }
            
            $keywords = explode(' ', $request->keyword);

            for ($i=0; $i < count($keywords); $i++) { 
                # code...
                if($i !== (count($keywords) - 1)){
                    $keywordQuery = $keywordQuery." (pcbox.nombre LIKE '%".$keywords[$i]."%') AND ";
                }
                else{
                    $keywordQuery = $keywordQuery." (pcbox.nombre LIKE '%".$keywords[$i]."%')";
                }
            }
        }
        // WHERE ( pcbox.referencia_fabricante = pccomponentes.referencia_fabricante AND pcbox.precio > pccomponentes.precio) AND 
        // (( pcbox.nombre LIKE '%placa%' AND pcbox.nombre LIKE '%gigabyte%') OR ( pcbox.nombre LIKE '%asus%') AND pcbox.nombre LIKE '%grafica%' ORDER BY pccomponentes.precio

        // Check if additional keyword search field has been used
        if($request->keyword1 != null){
            $keywordQuery = $keywordQuery." OR ";
            $keywords1 = explode(' ', $request->keyword1);

            for ($i=0; $i < count($keywords1); $i++) { 
                # code...
                if($i !== (count($keywords1) - 1)){
                    $keywordQuery = $keywordQuery." (pcbox.nombre LIKE '%".$keywords1[$i]."%') AND ";
                }
                else{
                    $keywordQuery = $keywordQuery." (pcbox.nombre LIKE '%".$keywords1[$i]."%')";
                }
            }
            $keywordQuery = $keywordQuery.")";
        }

        // Evaluamos si hay filtro de menor o mayor
        switch($request->comparison){
            case "lesser":
                $comparisonQuery = " AND pcbox.precio <= pccomponentes.precio";
                break;

            case "greater":
                $comparisonQuery = " AND pcbox.precio > pccomponentes.precio";
                break;

            default:
                $comparisonQuery = "";
                break;

        }

        $tempList = DB::select(
            "SELECT pcbox.codigo, pcbox.nombre, pcbox.precio, pcbox.enlace as enlace, pccomponentes.referencia_fabricante,
            pccomponentes.precio as precioPccomp, pccomponentes.enlace as enlacePccomp FROM pcbox,pccomponentes WHERE ("
            .$categoryQuery." pcbox.referencia_fabricante = pccomponentes.referencia_fabricante".$comparisonQuery.") "
            .$keywordQuery." ORDER BY pccomponentes.precio"
        );

        if($request->percentage != null){
            $products = array();

            if($request->comparison != "all"){
                foreach($tempList as $product){
                    // Division by zero error needs to be controlled
                    if($product->precio != 0){
                        if (abs((($product->precioPccomp - $product->precio) / $product->precio) * 100) <= abs($request->percentage)){
                            array_push($products, $product);
                        };
                    }
                };
            }
            else{
                $products = $tempList;
            }
        }
        else{
            $products = $tempList;
        }

        $totalPCB = 0.0; $totalPCC = 0.0;

        foreach ($products as $index => $product) {
            # code...
            $product->nombre = strtoupper($product->nombre);
            if($product->precio !=0){
                $products[$index]->difference = round($product->precioPccomp - $product->precio, 2, PHP_ROUND_HALF_UP);
                $products[$index]->percentage = round(($product->difference / $product->precio) * 100, 2, PHP_ROUND_HALF_UP);
                $totalPCB += $product->precio;
                $totalPCC += $product->precioPccomp;
            }
            else{
                $products[$index]->precio = "Consultar";
                $products[$index]->difference = null;
                $products[$index]->percentage = null;
            }
            
        }

        $totalDifference = $totalPCC - $totalPCB;
        if($totalPCB != 0){
            $totalPercentage = round(($totalDifference / $totalPCB) * 100, 2);
        }
        else{
            $totalPercentage = null;
        }

        if(count($products) > 0) {
            $results = array();
            $results['products'] = $products;
            $results['totalPCB'] = $totalPCB;
            $results['totalPCC'] = $totalPCC;
            $results['totalDifference'] = $totalDifference;
            $results['totalPercentage'] = $totalPercentage;
            return response()->json(['productsArray' => $results], 200);
        }
        
        return response()->json(['error' => 'No products were found. Please, try again with different search parameters']);
    }

    /**
     * Function to delete blank spaces from a string
     * @param String $string
     * @return String string properly formatted
     */
    private function trimAndFormat(String $string){
        return str_replace(" ", "", $string);
    }

    /**
     * Function to split comma divided string into an array
     * @param String $string
     * @return Array string exploded/split
     */
    private function explodeString(String $string){
        return explode(',', $string);
    }
}
