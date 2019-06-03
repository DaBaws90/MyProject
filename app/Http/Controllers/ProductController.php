<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pccomponente;
use App\Models\Familium;
use Illuminate\Support\Facades\DB;
// use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use Smalot\PdfParser\Parser;
use App\Upload;
// use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Pcbox;

use App\Rules\pdfLimitReached;

class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('products.index');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('products.addView');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [

        ]);
        
        $product = Pccomponente::create($request->all());

        if($product) {
            return redirect()->route('poducts.index')->with('message', ['success' => 'Producto creado satisfactoriamente']);
        }
        else{
            return redirect()->route('products.index')->with('message', ['danger' => 'Se produjo un error al crear el producto']);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Pccomponente::find($id);
        return view('products.details', compact('product'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [

        ]);

        $product = Pccomponente::find($id);
        $success = $product->update($request->all());
        
        if($success){
            return redirect()->route('products.index')->with('message', ['success' => 'Producto actualizado satisfactoriamente']);
        }
        else{
            return redirect()->route('products.editView')->with('message', ['danger' => 'Se produjo un error al actualizar el producto']);
        }

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $success = Pccomponente::destroy($id);

        if($success){
            return redirect()->route('products.index')->with('message', ['success' => 'Se eliminó el producto satisfactoriamente']);
        }
        else{
            return redirect()->route('products.index')->with('message', ['danger' => 'Se produjo un error al eliminar el producto']);
        }
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

        $products = isset($assoc_array['products']) ? collect($assoc_array['products']) : array();
        $totalPCB = isset($assoc_array['totalPCB']) ? $assoc_array['totalPCB'] : 0.0;
        $totalPCC = isset($assoc_array['totalPCC']) ? $assoc_array['totalPCC'] : 0.0;
        $totalDifference = isset($assoc_array['totalDifference']) ? $assoc_array['totalDifference'] : null;
        $totalPercentage = isset($assoc_array['totalPercentage']) ? $assoc_array['totalPercentage'] : null;

        // $this->showResults($request);
        // return json_encode($products);
        // return Datatables::of($products)->make(true);
        return view('products.results', compact('products', 'totalPCB', 'totalPCC', 'totalDifference', 'totalPercentage'))->with(['title' => 'referencias', 'generate' => true]);
        
    }

    /**
     * Function to delete blank spaces from a string
     * 
     * @param String $string
     * @return String string properly formatted
     */
    private function trimAndFormat(String $string){
        return str_replace(" ", "", $string);
    }

    /**
     * Function to split comma divided string into an array
     * 
     * @param String $string
     * @return Array string exploded/split
     */
    private function explodeString(String $string){
        return explode(',', $string);
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
            "SELECT pcbox.codigo, pcbox.nombre, pcbox.precio, pcbox.enlace as enlace, pcbox.subcategoria, pccomponentes.referencia_fabricante,
            pccomponentes.precio as precioPccomp, pccomponentes.enlace as enlacePccomp FROM pcbox,pccomponentes WHERE ("
            .$categoryQuery." pcbox.referencia_fabricante = pccomponentes.referencia_fabricante".$comparisonQuery.") "
            .$keywordQuery." ORDER BY pccomponentes.precio"
        );

        // OLD QUERY - Reference for consulting
        // $tempList = DB::select("SELECT pcbox.codigo, pcbox.nombre, pcbox.precio, pcbox.enlace as enlace, pccomponentes.referencia_fabricante,
        //     pccomponentes.precio as precioPccomp, pccomponentes.enlace as enlacePccomp FROM pcbox,pccomponentes WHERE"
        //     .$categoryQuery." pcbox.referencia_fabricante = pccomponentes.referencia_fabricante"
        //     .$keywordQuery." ".$comparisonQuery." ORDER BY pccomponentes.precio");


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

        // Once all the refs have been processed, calls to totals function to get the total prices and differences between companies, saving data in an associative array
        $assoc_array = $this->totals($products);

        $products = $assoc_array['products'];
        $totalPCB = $assoc_array['totalPCB'];
        $totalPCC = $assoc_array['totalPCC'];
        $totalDifference = $assoc_array['totalDifference'];
        $totalPercentage = $assoc_array['totalPercentage'];
        
        // Insert the data into the view and returns it
        return view('products.results', compact('products', 'totalPCB', 'totalPCC', 'totalDifference', 'totalPercentage'))->with(['title' => 'familias', 'generate' => true]);
    }

    /**
     * Display the specified resources
     * 
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generateAlternativeBudget(Request $request) {

        Validator::make($request->all(), [
            'percentage' => 'bail|sometimes|numeric|min:0|max:100|nullable',
            'comparison' => 'required',
            'products' => 'required',
            'keyword' => 'bail|sometimes|nullable|string|max:150',
        ])->validate();

        // if ($validator->fails()) {
        //     return response()->json(['errors' => $validator->messages()], 422);
        // }

        // Logic starts here -------------------------------------------------------
        $resultsArray = array();

        // Keyword's input value handler
        $keywordQuery = "";

        if ($request->keyword != null) {
            
            $keywordQuery = " AND (";

            $kwordList = explode(' ', $request->keyword);

            for ($i = 0; $i < count($kwordList) ; $i++) { 
                if ($i < count($kwordList) - 1) {
                    $keywordQuery = $keywordQuery . " (nombre LIKE '%".$kwordList[$i]."%') AND";
                }
                else {
                    $keywordQuery = $keywordQuery . " (nombre LIKE '%".$kwordList[$i]."%') ";
                }
            }

            $keywordQuery = $keywordQuery . ")";
        }

        // Alternative products handler
        $total = 0.0;
        $totalPCC = 0.0;

        $oldProducts = array();
        $alternatives = array();

        foreach (json_decode($request->products) as $index => $product) {

            $delta = 0.0;

            if ($request->percentage != 0) {

                switch ($request->comparison) {

                    case 'lesser':
                        $delta = $product->precioPccomp - (($product->precioPccomp * $request->percentage) / 100);
                        $percentageQuery = " AND precio > ".$delta." AND precio <= ".$product->precioPccomp;
                        break;
    
                    case 'greater':
                        $delta = $product->precioPccomp + (($product->precioPccomp * $request->percentage) / 100);
                        $percentageQuery = " AND precio <= ".$delta." AND precio > ".$product->precioPccomp;
                        break;
                    
                    default:
                        $delta = $product->precioPccomp + (($product->precioPccomp * $request->percentage) / 100);
                        $percentageQuery = " AND precio NOT IN (0) AND precio <= ".$delta;
                        break;
                }

            }
            else {

                $percentageQuery = ($request->comparison == 'lesser') ? " AND precio NOT IN (0) AND precio <= ".$product->precioPccomp 
                    : (($request->comparison == "greater") ? " AND precio > ".$product->precioPccomp : " AND precio NOT IN (0)");

            }

            $queryResult = DB::select(
                "SELECT * FROM pcbox WHERE ( subcategoria LIKE '".$product->subcategoria."'  
                    ".$percentageQuery." ) ".$keywordQuery." ORDER BY precio"
            );

            $total += $product->precio;
            $totalPCC += $product->precioPccomp;

            array_push($oldProducts, $product);
            array_push($alternatives, $queryResult ? $queryResult : null);

        }

        if ($oldProducts != null && $alternatives != null) {
            return  response()->json(['success' => [
                'mssg' => "Alternative budget successfully generated. <br/> Click 'Ok' to get redirected, otherwise, click 'Cancel'",
                'oldProducts' => collect($oldProducts),
                'alternatives' => collect($alternatives),
                'totals' => array($total, $totalPCC),
                'nextRoute' => route('choices')
            ]], 200);
        }
            
        return response()->json(['errors' => ['mssg' => 'An error occured while generating the budget. Please, try again later.']], 500);

    }

    /**
     * Shows the alternative budget results view
     * 
     * @param Ajax Request $request
     * @return \Illuminate\Http\Response
     */
    public function showAlternativeResults(Request $request) {

        $oldProducts = json_decode($request->oldProducts);
        $alternatives  = json_decode($request->alternatives);

        $total = round($request->totals[0], 2, PHP_ROUND_HALF_UP);
        $totalPCC = round($request->totals[1], 2, PHP_ROUND_HALF_UP);

        return response()->json([
            'view' => view('products.alternativesChoices', compact('oldProducts', 'alternatives', 'total', 'totalPCC'))->render()
        ]);
    }

    /**
     * Proccess the alternatives and generates a budget with them
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function processAlternativeBudget(Request $request) {

        $products = collect();

        foreach ($request->choices as $index => $productCode) {
            # code...
            if ($productCode != null) {
                $existsOrNot = Pcbox::where('codigo', $productCode)->get();
                if ($existsOrNot) {
                    $products->push($existsOrNot[0]);
                }
            }
        }

        $assoc_array = $this->totals($products);

        $products = isset($assoc_array['products']) ? collect($assoc_array['products']) : array();
        $totalPCB = isset($assoc_array['totalPCB']) ? $assoc_array['totalPCB'] : 0.0;
        $totalPCC = isset($assoc_array['totalPCC']) ? $assoc_array['totalPCC'] : 0.0;
        $totalDifference = isset($assoc_array['totalDifference']) ? $assoc_array['totalDifference'] : null;
        $totalPercentage = isset($assoc_array['totalPercentage']) ? $assoc_array['totalPercentage'] : null;

        return view('products.results', compact('products', 'totalPCB', 'totalPCC', 'totalDifference', 'totalPercentage'))->with(['title' => 'productos alternativos', 'generate' => false]);

    }

    /**
     * Process text from an uploaded PDF (using smalot/pdfparser)
     * 
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function pdfUpload(Request $request, $newFile = false){

        // if(count(Auth::user()->uploads) >= 1) {
        //     return redirect()->back()->with('errors', ['file' => 'You cannot upload more than 10 budgets']);
        // }

        if($newFile) {
            // Calls AUX function to manage the file upload
            $this->uploadFile($request, 'file', 'pdf');
        }

        if(!$request->id) {
            // The relative or absolute path to the PDF file
            $pdfPath = $request->file('file')->path();
        }
        else {
            $file = Upload::find($request->id);
            $pdfPath = Storage::disk('uploads')->getAdapter()->getPathPrefix() . $file->path;
        }
        
        // Create an instance of the PDFParser
        $parser = new Parser();

        // Create an instance of the PDF with the parseFile method of the parser
        // this method expects as first argument the path to the PDF file
        $pdf = $parser->parseFile($pdfPath);

        // Retrieve all pages from the pdf file.
        $pages  = $pdf->getPages();

        // Create an empty variable that will store the resulting text
        $text = "";
         
        // Loop over each page to extract the text
        foreach ($pages as $page) {
            // Concat the extracted text from every page into a var
            $text .= preg_split("/(.*COMPONENTES)/", $page->getText())[1];
        }

        // Filters the resulting text, splitting it in case strings *SERV*, *FORMA* or *9001* appears on it, whichever it occurs
        $filteredTexBlock = preg_split("/(Forma)/", $text)[0];


        // A partir de aquí CREO que se podría hacer en la función AUX --------------------------

        // Split the text into lines
        $linesSplitText = preg_split("/(\n)/", $filteredTexBlock);

        $products = array();

        // GEENRAR FUNCION AUX PARA EL PROCESADO DEL TEXTO ---------------------------------

        // Iterates over every line except the first and the last one
        foreach(array_splice($linesSplitText, 1, -1) as $line){

            // Gets the reference, which is at the beggining at the lane in case there is a valid one in that lane
            $ref = explode(' ', $line)[0];

            // Filter the "possible" reference with the following filters -> Could start with a #, and only contains letters (one or more occurrences) a-zA-Z 
            if(!preg_match("/^\#?[a-zA-Z]+$/", $ref)) {

                // Query the database with the extracted text as reference
                $isRefOrNot = DB::select(DB::raw("SELECT pcbox.codigo, pcbox.nombre, pcbox.precio, pcbox.enlace as enlace, pccomponentes.referencia_fabricante, 
                pccomponentes.precio as precioPccomp, pccomponentes.enlace as enlacePccomp, pcbox.subcategoria 
                    FROM pccomponentes 
                    JOIN pcbox ON pccomponentes.referencia_fabricante = pcbox.referencia_fabricante 
                    WHERE pcbox.codigo = :ref"), array('ref' => $ref)
                );

                // Check regex malfunctioning to avoid false positives, and if database query got a result with a valid reference, add the product into an array
                if($isRefOrNot){
                    array_push($products, $isRefOrNot[0]);
                }

            }

        }

        // Once all the refs have been processed, calls to totals function to get the total prices and differences between companies, saving data in an associative array
        $assoc_array = $this->totals($products);

        $products = isset($assoc_array['products']) ? collect($assoc_array['products'])->sortBy('precioPccomp') : array();
        $totalPCB = isset($assoc_array['totalPCB']) ? $assoc_array['totalPCB'] : 0.0;
        $totalPCC = isset($assoc_array['totalPCC']) ? $assoc_array['totalPCC'] : 0.0;
        $totalDifference = isset($assoc_array['totalDifference']) ? $assoc_array['totalDifference'] : null;
        $totalPercentage = isset($assoc_array['totalPercentage']) ? $assoc_array['totalPercentage'] : null;

        // Insert the data into the view and returns it
        return view('products.results', compact('products', 'totalPCB', 'totalPCC', 'totalDifference', 'totalPercentage'))->with(['title' => 'referencias del PDF', 'generate' => true]);
    }


    /**
     * Replaces a substring with the given arguments 
     * 
     * @param String $string to replace into
     * @param Array/String $extensions with the values to insert into $string
     */
    public function replaceSubstring($string, $extensions) {
        
        $extensionList = "";

        if(is_array($extensions)) {
            
            foreach ($extensions as $index => $ext) {
                if($index == count($extensions) -1) {
                    $extensionList .= $ext;
                }
                else {
                    $extensionList .= $ext.",";
                }
            }
        }
        else {
            $extensionList .= $extensions;
        }

        return str_replace("dummy", $string, $extensionList);
    }


    /**
     * Manages the file uploads
     * 
     * @param Illuminate\Http\Request $request
     * @param String $fileType
     * @param Array/String $extensions
     * @return String $fileName
     */
    public function uploadFile(Request $request, $fileType, $extensions) {

        // $count = count(Auth::user()->uploads);

        Validator::make($request->all(),[
            $fileType => [
                'required', $fileType, 'mimes:'.$this->replaceSubstring('dummy', $extensions), new pdfLimitReached(),
            ]
        ])->validate();

        // Generates the valid relative path for default storage driver (LOCAL I GUESS) and concats the specified string path 
        $uploadPath = Auth::user()->id .'/'. $fileType;
        
        // Gets the original uploaded file
        $originalName = $request->file($fileType)->getClientOriginalName();

        // Check if the current authenticated user already has a folder. 
        if(!Storage::exists($uploadPath)) {

            // If not, creates the directory and saves the file. Else, only saves the file in current user's directory
            Storage::makeDirectory($uploadPath);

        }
        else {
            // Checks if file already exists to "overwritte" it
            if(Storage::exists($uploadPath .'/'. $originalName)) {
                
                // Gets the upload record from database
                $uploadInstance = Upload::where('filename', $originalName)->get();

                // Check if there is actually a value stored in database 
                if(count($uploadInstance) > 0) {

                    // Deletes the record from database
                    $uploadInstance[0]->destroy($uploadInstance[0]->id);

                    // Deletes the existent file --- NO NED FOR THIS, SINC putFileAs WILL OVERWRITE THE EXISTING FILE
                    // Storage::delete($uploadInstance[0]->path);
                }

            }
            
        }

        // Creates the upload record
        $uploadInstance = Upload::create([
            'filename' => $originalName,
            'path' => $uploadPath .'/'. $originalName,
        ]);

        // Stores the file with the original client name
        Storage::putFileAs($uploadPath, $request->file($fileType), $originalName);

        return $uploadInstance;
    }


    /**
     * 
     * Calculates the total prices and differences for the given products
     * 
     * @param Array containing products to operate with them
     * @return Array with desired values
     */
    private function totals($products, $alternativeTotals = false){

        // Defines an array
        $assoc_array = array();

        if($products) {

            $totalPCB = 0.0; $totalPCC = 0.0;

            // Iterates over the products
            foreach ($products as $index => $product) {
                // Check if the current product has a price set and, if not, avoid the division by zero, and set the values as null
                if($product->precio !=0){
                    // If price of current product has a value different than 0, set a pair key => value for both difference and percentage, and agrees prices to totals vars
                    if (!isset($product->precioPccomp)) {

                        $product->difference = null;
                        $product->percentage = null;
                        $totalPCB += $product->precio;
                    }
                    else {
                        $products[$index]->difference = $product->precioPccomp - $product->precio;
                        $products[$index]->percentage = round(($product->difference / $product->precio) * 100, 2);
                        $totalPCB += $product->precio;
                        $totalPCC += $product->precioPccomp;
                    }
                }
                else{
                    // Set values as null if no price has been set for current product
                    $totalPCC = isset($product->precioPccomp) ? $totalPCC + $product->precioPccomp : null;
                    $products[$index]->difference = null;
                    $products[$index]->percentage = null;
                }
            }
            
            // Gets the total difference
            $totalDifference = $totalPCC != null ? $totalPCC - $totalPCB : null;
            $totalPercentage = $totalPCB != 0 ? round(($totalDifference / $totalPCB) * 100, 2) : null;

            // Saves data into the associative array and returns it
            $assoc_array['products'] = $products;
            $assoc_array['totalPCB'] = $totalPCB;
            $assoc_array['totalPCC'] = $totalPCC;
            $assoc_array['totalDifference'] = $totalDifference;
            $assoc_array['totalPercentage'] = $totalPercentage;

        }

        return $assoc_array;
    }
    
}

