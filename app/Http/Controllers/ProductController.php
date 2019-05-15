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
        return view('products.results', compact('products', 'totalPCB', 'totalPCC', 'totalDifference', 'totalPercentage'))->with('title', 'referencias');
        
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
        return view('products.results', compact('products', 'totalPCB', 'totalPCC', 'totalDifference', 'totalPercentage'))->with('title', 'familias');
    }

    /**
     * Display the specified resources
     * 
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generateAlternativeBudget(Request $request) {
        // return response()->json(['errors' => ['mssg' => 'Unable to update the record. Please, try again later.']], 422);
        // return response()->json(['success' => ['mssg' => "Alternative budget successfully generated. <br/> Click 'Ok' to get redirected, otherwise, click 'Cancel'"]], 200);

        // $validator = 
        Validator::make($request->all(), [
            'percentage' => 'bail|sometimes|numeric|min:0.1|max:100|nullable',
            'comparison' => 'required',
            'products' => 'required',
            'keyword' => 'bail|sometimes|nullable|string|max:150',
        ])->validate();

        // if ($validator->fails()) {
        //     return response()->json(['errors' => $validator->messages()], 422);
        // }

        // Logic starts here -------------------------------------------------------
        $resultsArray = array();
        $resultsArray['oldProducts'] = array();
        $resultsArray['alternatives'] = array();

        $operatorQuery = $request->comparison == 'lesser' ? " AND precio < " : " AND precio >= ";

        foreach (json_decode($request->products) as $index => $product) {
            # code...
            array_push($resultsArray['oldProducts'], $product);

            $queryResult = DB::select(
                "SELECT * FROM pcbox WHERE ( subcategoria LIKE '".$product->subcategoria."' 
                    ".$operatorQuery." ".$product->precioPccomp.") ORDER BY precio"
            );

            if ($queryResult) {
                array_push($resultsArray['alternatives'], $queryResult);
            }
            
        }
        dd($resultsArray);

        $assoc_array = $this->totals($resultsArray);

        $products = isset($assoc_array['products']) ? collect($assoc_array['products']) : array();
        $totalPCB = isset($assoc_array['totalPCB']) ? $assoc_array['totalPCB'] : 0.0;
        $totalPCC = isset($assoc_array['totalPCC']) ? $assoc_array['totalPCC'] : 0.0;
        $totalDifference = isset($assoc_array['totalDifference']) ? $assoc_array['totalDifference'] : null;
        $totalPercentage = isset($assoc_array['totalPercentage']) ? $assoc_array['totalPercentage'] : null;

    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    // public function messages() {
    //     return [
    //         'products.required' => 'You need at least one product to generate an alternative budget (Necesita al menos un producto para generar un presupuesto alternativo)',
    //     ];
    // }

    /**
     * Process text from an uploaded PDF (using smalot/pdfparser)
     * 
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function pdfUpload(Request $request, $newFile = false){

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
                pccomponentes.precio as precioPccomp, pccomponentes.enlace as enlacePccomp 
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
        return view('products.results', compact('products', 'totalPCB', 'totalPCC', 'totalDifference', 'totalPercentage'))->with('title', 'referencias del PDF');
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

        // Validates that uploaded file is actually a PDF file using an AUX function
        $request->validate([
            $fileType => 'required|'.$fileType.'|mimes:'.$this->replaceSubstring('dummy', $extensions),
        ]);

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
    private function totals($products){

        $totalPCB = 0.0; $totalPCC = 0.0;

        // Iterates over the products
        foreach ($products as $index => $product) {
            // Check if the current product has a price set and, if not, avoid the division by zero, and set the values as null
            if($product->precio !=0){
                // If price of current product has a value different than 0, set a pair key => value for both difference and percentage, and agrees prices to totals vars
                $products[$index]->difference = $product->precioPccomp - $product->precio;
                $products[$index]->percentage = round(($product->difference / $product->precio) * 100, 2);
                $totalPCB += $product->precio;
                $totalPCC += $product->precioPccomp;
            }
            else{
                // Set values as null if no price has been set for current product
                $products[$index]->difference = null;
                $products[$index]->percentage = null;
            }
            
        }

        // Gets the total difference
        $totalDifference = $totalPCC - $totalPCB;

        // Gets the total percentage if total price has a value, else, set value as null
        if($totalPCB != 0){
            $totalPercentage = round(($totalDifference / $totalPCB) * 100, 2);
        }
        else{
            $totalPercentage = null;
        }

        // Defines an array
        $assoc_array = array();

        // Saves data into the associative array and returns it
        $assoc_array['products'] = $products;
        $assoc_array['totalPCB'] = $totalPCB;
        $assoc_array['totalPCC'] = $totalPCC;
        $assoc_array['totalDifference'] = $totalDifference;
        $assoc_array['totalPercentage'] = $totalPercentage;

        return $assoc_array;
    }

    // public function setArrayValues($assoc_array) {
    //     $array = array();

    //     $array['products'] = isset($assoc_array['products']) ? collect($assoc_array['products'])->sortBy('precioPccomp') : array();
    //     $array['totalPCB'] = isset($assoc_array['totalPCB']) ? $assoc_array['totalPCB'] : 0.0;
    //     $array['totalPCC'] = isset($assoc_array['totalPCC']) ? $assoc_array['totalPCC'] : 0.0;
    //     $array['totalDifference'] = isset($assoc_array['totalDifference']) ? $assoc_array['totalDifference'] : null;
    //     $array['totalPercentage'] = isset($assoc_array['totalPercentage']) ? $assoc_array['totalPercentage'] : null;
        
    //     return $array;
    // }


    /**
     * Function for testing purpouses only
     * 
     * @return json containing listed resources
     */
    public function dummy(Request $request){
        // Retrieving users data
        $products = DB::table('users');

        $products = [
            'data' => $products->get()->all()
        ];

        // return Datatables::of(User::query())->make(true);
        return json_encode($products);
    }

    
}

