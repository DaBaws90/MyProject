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
    // public function references(Request $request) {
        // $products = array();
        // $totalPCB = 0.0; $totalPCC = 0.0; $totalDifference = 0.0; $totalPercentage = 0.0;

        // if($request->references != null){
        //     $productsRefs = $this->explodeString($request->references);

        //     foreach ($productsRefs as $ref) {
        //         $existsOrNot = DB::select("SELECT pcbox.codigo, pcbox.nombre, pcbox.precio, pcbox.enlace as enlace, pccomponentes.referencia_fabricante,
        //             pccomponentes.precio as precioPccomp, pccomponentes.enlace as enlacePccomp FROM pcbox,pccomponentes WHERE pcbox.codigo LIKE '".$this->trimAndFormat($ref)."'
        //             AND pcbox.referencia_fabricante = pccomponentes.referencia_fabricante");

        //         if($existsOrNot) {
        //             array_push($products, $existsOrNot[0]);
        //         }
        //     }

    //         foreach ($products as $index => $product) {
    //             # code...
    //             $product->nombre = strtoupper($product->nombre);
    //             if($product->precio !=0){
    //                 $products[$index]->difference = round($product->precioPccomp - $product->precio, 2, PHP_ROUND_HALF_UP);
    //                 $products[$index]->percentage = round(($product->difference / $product->precio) * 100, 2, PHP_ROUND_HALF_UP);
    //                 $totalPCB += $product->precio;
    //                 $totalPCC += $product->precioPccomp;
    //             }
    //             else{
    //                 $products[$index]->precio = "Consultar";
    //                 $products[$index]->difference = null;
    //                 $products[$index]->percentage = null;
    //             }
                
    //         }

    //         $totalDifference = $totalPCC - $totalPCB;
    //         if($totalPCB != 0){
    //             $totalPercentage = round(($totalDifference / $totalPCB) * 100, 2);
    //         }
    //         else{
    //             $totalPercentage = null;
    //         }
    //     }
    //     else {
    //         return response()->json(['error' => 'The search input was empty. Please, type something in order to search products']);
    //     }

    //     if(count($products) > 0) {
    //         $results = array();
    //         $results['products'] = $products;
    //         $results['totalPCB'] = $totalPCB;
    //         $results['totalPCC'] = $totalPCC;
    //         $results['totalDifference'] = $totalDifference;
    //         $results['totalPercentage'] = $totalPercentage;
    //         return response()->json(['productsArray' => $results], 200);
    //     }
        
    //     return response()->json(['error' => 'No products were found. Please, try again with different search parameters']);
    // }

    /**
     * 
     * Calculates the total prices and differences for the given products
     * 
     * @param Array containing products to operate with them
     * @return Array with desired values
     */
    private function totals($products){

        $totalPCB = 0.0; $totalPCC = 0.0;

        // Iterates over the products
        foreach ($products as $index => $product) {
            $product->nombre = strtoupper($product->nombre);
            // Check if the current product has a price set and, if not, avoid the division by zero, and set the values as null
            if($product->precio !=0){
                // If price of current product has a value different than 0, set a pair key => value for both difference and percentage, and agrees prices to totals vars
                if (!isset($product->precioPccomp)) {

                    $product->difference = null;
                    $product->percentage = null;
                    $totalPCB += $product->precio;
                }
                else {
                    $products[$index]->difference = round($product->precioPccomp - $product->precio, 2, PHP_ROUND_HALF_UP);
                    $products[$index]->percentage = round(($product->difference / $product->precio) * 100, 2, PHP_ROUND_HALF_UP);
                    $totalPCB += $product->precio;
                    $totalPCC += $product->precioPccomp;
                }
            }
            else{
                // Set values as null if no price has been set for current product
                $totalPCC = isset($product->precioPccomp) ? $totalPCC + $product->precioPccomp : null;
                $products[$index]->difference = null;
                $products[$index]->percentage = null;
                $product->precio = "Consultar";
            }
        }    

        // Gets the total difference
        $totalDifference = $totalPCC != null ? round($totalPCC - $totalPCB, 2, PHP_ROUND_HALF_UP) : null;
        $totalPercentage = $totalPCB != 0 ? round(($totalDifference / $totalPCB) * 100, 2, PHP_ROUND_HALF_UP) : null;

        // Defines an array
        $assoc_array = array();
        
        if($products) {
            // Saves data into the associative array and returns it
            $assoc_array['products'] = $products;
            $assoc_array['totalPCB'] = $totalPCB;
            $assoc_array['totalPCC'] = $totalPCC;
            $assoc_array['totalDifference'] = $totalDifference;
            $assoc_array['totalPercentage'] = $totalPercentage;
        }

        return $assoc_array;
    }

    /**
     * Display the specified resources.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function references(Request $request){

        if($request->references != null){
            $productsTemp = array();
            $productsRefs = $this->explodeString($request->references);

            foreach ($productsRefs as $ref) {
                $queryResult = DB::select("SELECT pcbox.codigo, pcbox.nombre, pcbox.precio, pcbox.enlace as enlace, pcbox.subcategoria, pccomponentes.referencia_fabricante,
                pccomponentes.precio as precioPccomp, pccomponentes.enlace as enlacePccomp FROM pcbox,pccomponentes WHERE pcbox.codigo LIKE '".$this->trimAndFormat($ref)."'
                AND pcbox.referencia_fabricante = pccomponentes.referencia_fabricante");

                if($queryResult){
                    array_push($productsTemp, $queryResult[0]);
                }
                
            }

            $assoc_array = $this->totals($productsTemp);
        }
        else {
            return response()->json(['error' => 'The search input was empty. Please, type something in order to search products']);
        }
        // Analizar bien esta parte

        // $products = isset($assoc_array['products']) ? collect($assoc_array['products']) : array();
        // $totalPCB = isset($assoc_array['totalPCB']) ? $assoc_array['totalPCB'] : 0.0;
        // $totalPCC = isset($assoc_array['totalPCC']) ? $assoc_array['totalPCC'] : 0.0;
        // $totalDifference = isset($assoc_array['totalDifference']) ? $assoc_array['totalDifference'] : null;
        // $totalPercentage = isset($assoc_array['totalPercentage']) ? $assoc_array['totalPercentage'] : null;

        if($assoc_array) {
            return response()->json(['productsArray' => $assoc_array], 200);
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
