<?php

namespace App\Http\Controllers\Panel;

use App\Http\Requests\ProductRequest;
use App\Models\PanelProduct;
use App\Http\Controllers\Controller;
use App\Scopes\AvailableScopes;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{

    public function index() {

        return view('products.index')->with([
                                    'products'=>PanelProduct::without('images')->get(),
                                    ]);
    }

    public function create(){
        return view('products.create');
    }
    public function store(ProductRequest $request){

        /* $rules = [
            'title' => ['required', 'max:255'],
            'description' => ['required', 'max:1000'],
            'price' => ['required', 'min:1'],
            'stock' => ['required', 'min:0'],
            'status' => ['required', 'in:available,unavailable']

        ];
        request()->validate($rules); */


        /* if ($request()->status == 'available' && $request()->stock == 0){
            return redirect()
                        ->back()
                        ->withInput($request()->all())
                        ->withErrors ('If the product is available must have stock');
        } */


        /* $product = Product::create([
            'title'=> request()->title,
            'description'=> request()->description,
            'price'=> request()->price,
            'stock'=> request()->stock,
            'status'=> request()->status
        ]); */
        $product = PanelProduct::create($request->validated());

        foreach ($request->images as $image) {
            $product->images()->create([
                'path'=> 'images/'. $image->store('products', 'images')
            ]);
        }

        /* return redirect()->back(); */
        /* return redirect()->action('MainController@index'); */
        return redirect()
                    ->route('products.index')
                    ->withSuccess("The new product with {$product->id} was created");


    }
    public function show( PanelProduct $product){
        /* $product = Product::findOrFail($product); SE REEMPLAZA CON LA INYECCION IMPLICITA DEL MODELO*/

        return view('products.show')->with([
                                    'product'=>$product,
                                    ]);
    }
    public function edit( PanelProduct $product){

        return view('products.edit')->with([
            'product'=> $product,
        ]);
    }
    public function update(ProductRequest $request, PanelProduct $product){

        /* $rules = [
            'title' => ['required', 'max:255'],
            'description' => ['required', 'max:1000'],
            'price' => ['required', 'min:1'],
            'stock' => ['required', 'min:0'],
            'status' => ['required', 'in:available,unavailable']

        ];
        request()->validate($rules); */

        /* $product = Product::findOrFail("$product"); SE REEMPLAZA CON LA INYECCION IMPLICITA DEL MODELO*/

        $product->update($request->validated());

        if ($request->hasFile('images')) {
            foreach ($product->images as $image) {
                $path = storage_path("app/public/{$image->path}");

                File::delete($path);

                $image->delete();
            }

            foreach ($request->images as $image) {
                $product->images()->create([
                    'path'=> 'images/'. $image->store('products', 'images')
                ]);
            }
        }


        return redirect()
                ->route('products.index')
                ->withSuccess("The product with {$product->id} was edited");
    }
    public function destroy(PanelProduct $product){

        /* $product = Product::findOrFail($product); SE REEMPLAZA CON LA INYECCION IMPLICITA DEL MODELO*/

        $product->delete();

        return redirect()
                ->route('products.index')
                ->withSuccess("The product with {$product->id} was deleted");
    }
}
