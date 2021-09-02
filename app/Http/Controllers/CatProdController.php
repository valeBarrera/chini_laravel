<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Category;
use App\CharacteristicCategory;
use App\CharacteristicProductNative;
use App\ImageSide;
use App\ImageType;
use App\Product;
use App\TypeCharacteristicCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;

class CatProdController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(
            [
                'allCategory',
                'allCategoryWithCharacteristics',
                'allProduct',
                'allBrand',
                'allImageType',
                'allImageSide'
            ]
        );
        $this->middleware('check.role:admin')->except(
            [
                'allCategory',
                'allProduct',
                'allCategoryWithCharacteristics',
                'allBrand',
                'allCharacteristicCategory',
                'allCharacteristicCategoryOfCategory',
                'allTypeCharacteristicCategory',
                'allTypeCharacteristicCategoryOfCategory',
                'allImageType',
                'allImageSide'
            ]
        );
    }

    /** Start - Product */
    public function createProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'description' => 'required|string|between:2,100',
            'is_custom' => 'required|boolean',
            'price' => 'required|integer|min:100',
            'stock' => 'required|integer|min:1',
            'category_id' => 'required|integer',
            'brand_id' => 'required|integer',
            'has_color' => 'required|boolean',
            'color' => 'nullable|string',
            'img' => 'required|file|mimes:jpeg,bmp,png,jpg',
        ]);

        if ($validator->fails()) {
            $resp = new \stdClass();
            $resp->errors = $validator->errors();
            $resp->state = false;
            $resp->message = 'Ha ocurrido un error.';
            return response()->json($resp, 200);
        }
        $path = $request->img->store('public/products');
        $path = str_replace("public/", "storage/", $path);

        $product = Product::create(array_merge(
            $validator->validated(),
            [
                'state' => 0 ,
                'img' => $path
            ]
        ));

        $product->state = 0;

        if (isset($request->img) && $request->img) {
            $path = $request->img->store('public/products');
            $path = str_replace("public/", "storage/", $path);
            $product->img = $path;
            $product->save();
        }



        $resp = new \stdClass();
        $resp->message = 'Producto exitosamente creado';
        $resp->products = Product::with(['brand', 'category'])->get();
        $resp->state = true;
        return response()->json($resp, 201);
    }

    public function editProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'description' => 'required|string|between:2,100',
            'is_custom' => 'required|boolean',
            'price' => 'required|integer|min:100',
            'stock' => 'required|integer|min:1',
            'category_id' => 'required|integer',
            'brand_id' => 'required|integer',
            'has_color' => 'required|boolean',
            'color' => 'nullable|string',
            'img' => 'required|file|mimes:jpeg,bmp,png,jpg',
        ]);

        if ($validator->fails()) {
            $resp = new \stdClass();
            $resp->errors = $validator->errors();
            $resp->state = false;
            $resp->message = 'Ha ocurrido un error.';
            return response()->json($resp, 200);
        }

        $product = Product::find($request->id);

        if ($product->name != $request->name) {
            $product->name = $request->name;
        }

        if ($product->description != $request->description) {
            $product->description = $request->description;
        }

        if ($product->price != $request->price) {
            $product->price = $request->price;
        }

        if ($product->stock != $request->stock) {
            $product->stock = $request->stock;
        }

        if ($product->is_custom != $request->is_custom) {
            $product->is_custom = $request->is_custom;
        }

        if ($product->category_id != $request->category_id) {
            $product->category_id = $request->category_id;
        }

        if ($product->brand_id != $request->brand_id) {
            $product->brand_id = $request->brand_id;
        }

        $product->save();

        $resp = new \stdClass();
        $resp->message = 'Categoría exitosamente editada';
        $resp->products = Product::with(['brand', 'category'])->get();
        $resp->state = true;
        return response()->json($resp, 201);
    }

    public function deleteProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $resp = new \stdClass();
            $resp->errors = $validator->errors();
            $resp->state = false;
            $resp->message = 'Ha ocurrido un error.';
            return response()->json($resp, 200);
        }

        $product = Product::find($request->id);
        if ($product != null) {
            $product->delete();
            $resp = new \stdClass();
            $resp->state = true;
            $resp->categorys = Product::all();
            $resp->message = 'Producto borrado exitosamente.';
            return response()->json($resp, 200);
        } else {
            $resp = new \stdClass();
            $resp->state = false;
            $resp->message = 'El producto no exíste.';
            return response()->json($resp, 200);
        }
    }

    public function allProduct(Request $request)
    {
        $resp = new \stdClass();
        $resp->state = true;
        $resp->message = 'Productos obtenidos';
        $resp->products = Product::with(['brand', 'category', 'category.charactCategorys' => function ($query) {
            $query->where('is_custom', 0);
        }, 'category.charactCategorys.typeCharactCategorys','characteristicsNative', 'characteristicsNative.typeCharactCategory'])->get();
        return response()->json($resp, 200);
    }

    public function saveCharacteristicProduct(Request $request)
    {
        $product = Product::find($request->product_id);
        if ($product != NULL) {
            foreach ($product->category->charactCategorys as $chrCat) {
                if (!$chrCat->is_custom) {
                    Log::debug($request[$chrCat->id]);
                    if ($request[$chrCat->id] != -1) {
                        $chrNative = NULL;
                        if ($request->has_native) {
                            $chrNative = CharacteristicProductNative::where(['product_id' => $product->id, 'characteristic_category_id' => $chrCat->id])->first();
                        }

                        if ($chrNative == NULL) {
                            $chrNative = new CharacteristicProductNative();
                            $chrNative->product_id =  $product->id;
                            $chrNative->characteristic_category_id =  $chrCat->id;
                        }

                        $chrNative->type_characteristic_category_id = $request[$chrCat->id];
                        $chrNative->save();
                    } else {
                        if ($request->has_native) {
                            $chrNative = CharacteristicProductNative::where(['product_id' => $product->id, 'characteristic_category_id' => $chrCat->id])->first();
                        }

                        if ($chrNative != NULL) {
                            $chrNative->delete();
                        }
                    }
                }
            }
            $resp = new \stdClass();
            $resp->state = true;
            $resp->message = 'Características actualizadas con éxito';
            $resp->products = Product::with(['brand', 'category', 'category.charactCategorys' => function ($query) {
                $query->where('is_custom', 0);
            }, 'category.charactCategorys.typeCharactCategorys', 'characteristicsNative', 'characteristicsNative.typeCharactCategory'])->get();
            return response()->json($resp, 200);
        } else {
            $resp = new \stdClass();
            $resp->state = false;
            $resp->message = 'Producto no existe';
            return response()->json($resp, 200);
        }

    }

    /** End - Product */

    /** Start - Category */
    public function createCategory(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'description' => 'required|string|between:2,100',
        ]);

        if ($validator->fails()) {
            $resp = new \stdClass();
            $resp->errors = $validator->errors();
            $resp->state = false;
            $resp->message = 'Ha ocurrido un error.';
            return response()->json($resp, 200);
        }

        $category = Category::create(array_merge(
            $validator->validated()
        ));

        $resp = new \stdClass();
        $resp->message = 'Categoría exitosamente creada';
        $resp->categorys = Category::all();
        $resp->state = true;
        return response()->json($resp, 201);
    }

    public function editCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'name' => 'required|string|between:2,100',
            'description' => 'required|string|between:2,100',
        ]);

        if ($validator->fails()) {
            $resp = new \stdClass();
            $resp->errors = $validator->errors();
            $resp->state = false;
            $resp->message = 'Ha ocurrido un error.';
            return response()->json($resp, 200);
        }

        $category = Category::find($request->id);

        if ($category->name != $request->name) {
            $category->name = $request->name;
        }

        if ($category->description != $request->description) {
            $category->description = $request->description;
        }

        $category->save();

        $resp = new \stdClass();
        $resp->message = 'Categoría exitosamente editada';
        $resp->categorys = Category::all();
        $resp->state = true;
        return response()->json($resp, 201);
    }

    public function deleteCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $resp = new \stdClass();
            $resp->errors = $validator->errors();
            $resp->state = false;
            $resp->message = 'Ha ocurrido un error.';
            return response()->json($resp, 200);
        }

        $category = Category::find($request->id);
        if ($category != null){
            $category->delete();
            $resp = new \stdClass();
            $resp->state = true;
            $resp->categorys = Category::all();
            $resp->message = 'Categoría borrada exitosamente.';
            return response()->json($resp, 200);
        }else{
            $resp = new \stdClass();
            $resp->state = false;
            $resp->message = 'La categoría no exíste.';
            return response()->json($resp, 200);
        }
    }

    public function allCategory(Request $request)
    {
        $resp = new \stdClass();
        $resp->state = true;
        $resp->message = 'Categorías obtenidas';
        $resp->categorys = Category::all();
        return response()->json($resp, 200);
    }

    public function allCategoryWithCharacteristics(Request $request)
    {
        $resp = new \stdClass();
        $resp->state = true;
        $resp->message = 'Categorías obtenidas';
        $resp->categorys = Category::all()->load(['charactCategorys' => function ($query) {
            $query->where('is_image', 0);
        }]);
        return response()->json($resp, 200);
    }

    public function allCategoryWithAllCharacteristics(Request $request)
    {
        $resp = new \stdClass();
        $resp->state = true;
        $resp->message = 'Categorías obtenidas';
        $resp->categorys = Category::all()->load(['charactCategorys']);
        return response()->json($resp, 200);
    }

    /** End - Category */

    /** Start - Brand */
    public function createBrand(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'description' => 'required|string|between:2,100',
        ]);

        if ($validator->fails()) {
            $resp = new \stdClass();
            $resp->errors = $validator->errors();
            $resp->state = false;
            $resp->message = 'Ha ocurrido un error.';
            return response()->json($resp, 200);
        }

        $brand = Brand::create(array_merge(
            $validator->validated()
        ));

        $resp = new \stdClass();
        $resp->message = 'Marca exitosamente creada';
        $resp->brands = Brand::all();
        $resp->state = true;
        return response()->json($resp, 201);
    }

    public function editBrand(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'name' => 'required|string|between:2,100',
            'description' => 'required|string|between:2,100',
        ]);

        if ($validator->fails()) {
            $resp = new \stdClass();
            $resp->errors = $validator->errors();
            $resp->state = false;
            $resp->message = 'Ha ocurrido un error.';
            return response()->json($resp, 200);
        }

        $brand = Brand::find($request->id);

        if ($brand->name != $request->name) {
            $brand->name = $request->name;
        }

        if ($brand->description != $request->description) {
            $brand->description = $request->description;
        }

        $brand->save();

        $resp = new \stdClass();
        $resp->message = 'Marca exitosamente editada';
        $resp->brands = Brand::all();
        $resp->state = true;
        return response()->json($resp, 201);
    }

    public function deleteBrand(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $resp = new \stdClass();
            $resp->errors = $validator->errors();
            $resp->state = false;
            $resp->message = 'Ha ocurrido un error.';
            return response()->json($resp, 200);
        }

        $brand = Brand::find($request->id);
        if ($brand != null) {
            $brand->delete();
            $resp = new \stdClass();
            $resp->state = true;
            $resp->brands = Brand::all();
            $resp->message = 'Marca borrada exitosamente.';
            return response()->json($resp, 200);
        } else {
            $resp = new \stdClass();
            $resp->state = false;
            $resp->message = 'La Marca no exíste.';
            return response()->json($resp, 200);
        }
    }

    public function allBrand(Request $request)
    {
        $resp = new \stdClass();
        $resp->state = true;
        $resp->message = 'Marcas obtenidas';
        $resp->brands = Brand::all();
        return response()->json($resp, 200);
    }

    /** End - Brand */

    /** Start - CharacteristicCategory */
    public function createCharacteristicCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'description' => 'required|string|between:2,100',
            'is_custom' => 'required|boolean',
            'is_optional' => 'required|boolean',
            'is_image' => 'required|boolean',
            'is_design' => 'required|boolean',
            'is_text' => 'required|boolean',
            'price' => 'required|integer',
            'state' => 'required|boolean',
            'category_id' => 'required|integer',
            'image_type_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            $resp = new \stdClass();
            $resp->errors = $validator->errors();
            $resp->state = false;
            $resp->message = 'Ha ocurrido un error.';
            return response()->json($resp, 200);
        }

        $chrtCat = CharacteristicCategory::create(array_merge(
            $validator->validated()
        ));

        $resp = new \stdClass();
        $resp->message = 'Caraterística de Categoría exitosamente creada';
        $resp->charactCategorys = CharacteristicCategory::all();
        $resp->state = true;
        return response()->json($resp, 201);
    }

    public function editCharacteristicCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'name' => 'required|string|between:2,100',
            'description' => 'required|string|between:2,100',
            'is_custom' => 'required|boolean',
            'is_optional' => 'required|boolean',
            'is_image' => 'required|boolean',
            'is_design' => 'required|boolean',
            'is_text' => 'required|boolean',
            'state' => 'required|boolean',
            'price' => 'required|integer',
            'category_id' => 'required|integer',
            'image_type_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            $resp = new \stdClass();
            $resp->errors = $validator->errors();
            $resp->state = false;
            $resp->message = 'Ha ocurrido un error.';
            return response()->json($resp, 200);
        }

        $chrtCat = CharacteristicCategory::find($request->id);

        if ($chrtCat->name != $request->name) {
            $chrtCat->name = $request->name;
        }

        if ($chrtCat->description != $request->description) {
            $chrtCat->description = $request->description;
        }

        if (isset($request->is_custom)) {
            $chrtCat->is_custom = ($request->is_custom) ? 1 : 0;
        }

        if (isset($request->is_optional)) {
            $chrtCat->is_optional = ($request->is_optional) ? 1 : 0;
        }

        if (isset($request->is_image)) {
            $chrtCat->is_image = ($request->is_image) ? 1 : 0;
        }

        if (isset($request->is_design)) {
            $chrtCat->is_design = ($request->is_design) ? 1 : 0;
        }

        if (isset($request->is_text)) {
            $chrtCat->is_text = ($request->is_text) ? 1 : 0;
        }

        if (isset($request->state)) {
            $chrtCat->state = ($request->state) ? 1 : 0;
        }

        if ($chrtCat->price != $request->price) {
            $chrtCat->price = $request->price;
        }

        if ($chrtCat->category_id != $request->category_id) {
            $chrtCat->category_id = $request->category_id;
        }

        if ($chrtCat->image_type_id != $request->image_type_id) {
            $chrtCat->image_type_id = $request->image_type_id;
        }

        $chrtCat->save();

        $resp = new \stdClass();
        $resp->message = 'Caraterística de Categoría exitosamente editada';
        $resp->charactCategorys = CharacteristicCategory::all()->load(['category', 'typeCharactCategorys']);
        $resp->state = true;
        return response()->json($resp, 201);
    }

    public function deleteCharacteristicCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $resp = new \stdClass();
            $resp->errors = $validator->errors();
            $resp->state = false;
            $resp->message = 'Ha ocurrido un error.';
            return response()->json($resp, 200);
        }

        $chrtCat = CharacteristicCategory::find($request->id);
        if ($chrtCat != null) {
            $chrtCat->delete();
            $resp = new \stdClass();
            $resp->state = true;
            $resp->charactCategorys = CharacteristicCategory::all()->load(['category', 'typeCharactCategorys']);
            $resp->message = 'Caraterística de Categoría borrada exitosamente.';
            return response()->json($resp, 200);
        } else {
            $resp = new \stdClass();
            $resp->state = false;
            $resp->message = 'La Caraterística de Categoría no exíste.';
            return response()->json($resp, 200);
        }
    }

    public function allCharacteristicCategory(Request $request)
    {
        $resp = new \stdClass();
        $resp->state = true;
        $resp->message = 'Caraterísticas de Categoría obtenidas';
        $resp->charactCategorys = CharacteristicCategory::all()->load(['category', 'typeCharactCategorys']);
        return response()->json($resp, 200);
    }

    public function allCharacteristicCategoryOfCategory(Request $request, $category_id)
    {
        $resp = new \stdClass();
        $resp->state = true;
        $resp->message = 'Caraterísticas de Categoría obtenidas';
        $resp->charactCategorys = Category::find($category_id)->charactCategorys;
        return response()->json($resp, 200);
    }
    /** End - CharacteristicCategory */

    /** Start - TypeCharacteristicCategory */
    public function createTypeCharacteristicCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'description' => 'required|string|between:2,100',
            'extra_price' => 'required|integer',
            'has_color' => 'required|boolean',
            'has_img' => 'required|boolean',
            'design_leaf' => 'nullable|integer',
            'state' => 'required|boolean',
            'color' => 'nullable|string',
            'img' => 'nullable|file|mimes:jpeg,bmp,png,jpg',
            'characteristic_categories_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $resp = new \stdClass();
            $resp->errors = $validator->errors();
            $resp->state = false;
            $resp->message = 'Ha ocurrido un error.';
            return response()->json($resp, 200);
        }

        $chrtCat = TypeCharacteristicCategory::create(array_merge(
            $validator->validated()
        ));

        if(isset($request->has_img) && $request->has_img){
            $path = $request->img->store('public/types');
            $path = str_replace("public/", "storage/", $path);
            $chrtCat->img = $path;
            $chrtCat->save();
        }

        $resp = new \stdClass();
        $resp->message = 'Tipo de Característica exitosamente creada';
        $resp->charactCategorys = CharacteristicCategory::all();
        $resp->state = true;
        return response()->json($resp, 201);
    }

    public function editTypeCharacteristicCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'name' => 'required|string|between:2,100',
            'description' => 'required|string|between:2,100',
            'extra_price' => 'required|integer',
            'has_color' => 'required|boolean',
            'has_img' => 'required|boolean',
            'design_leaf' => 'nullable|integer',
            'state' => 'required|boolean',
            'color' => 'nullable|boolean',
            'img' => 'nullable|file|mimes:jpeg,bmp,png,jpg',
            'characteristic_categories_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $resp = new \stdClass();
            $resp->errors = $validator->errors();
            $resp->state = false;
            $resp->message = 'Ha ocurrido un error.';
            return response()->json($resp, 200);
        }

        $chrtCat = TypeCharacteristicCategory::find($request->id);

        if ($chrtCat->name != $request->name) {
            $chrtCat->name = $request->name;
        }

        if ($chrtCat->description != $request->description) {
            $chrtCat->description = $request->description;
        }

        if ($chrtCat->characteristic_categories_id != $request->characteristic_categories_id) {
            $chrtCat->characteristic_categories_id = $request->characteristic_categories_id;
        }

        if (isset($chrtCat->state)) {
            $chrtCat->state = ($request->state) ? 1 : 0;
        }

        if ($chrtCat->extra_price != $request->extra_price) {
            $chrtCat->extra_price = $request->extra_price;
        }

        if (isset($request->has_color)) {
            $chrtCat->has_color = ($request->has_color) ? 1 : 0;
        }

        if ($request->has_color && $chrtCat->color != $request->color) {
            $chrtCat->color = $request->color;
        }

        if (isset($request->has_img)) {
            $chrtCat->has_img = ($request->has_img) ? 1 : 0;
        }

        if ($chrtCat->charactCategory->is_design){
            $chrtCat->design_leaf = $request->design_leaf;
        }

        if (isset($request->has_img) && $request->has_img) {
            $path = $request->img->store('public/types');
            $path = str_replace("public/", "storage/", $path);
            $chrtCat->img = $path;
            $chrtCat->save();
        }

        $chrtCat->save();

        $resp = new \stdClass();
        $resp->message = 'Tipo de Característica de Categoría exitosamente editada';
        $resp->typeCharactCategorys = CharacteristicCategory::all();
        $resp->state = true;
        return response()->json($resp, 201);
    }

    public function deleteTypeCharacteristicCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $resp = new \stdClass();
            $resp->errors = $validator->errors();
            $resp->state = false;
            $resp->message = 'Ha ocurrido un error.';
            return response()->json($resp, 200);
        }

        $chrtCat = TypeCharacteristicCategory::find($request->id);
        if ($chrtCat != null) {
            $chrtCat->delete();
            $resp = new \stdClass();
            $resp->state = true;
            $resp->charactCategorys = TypeCharacteristicCategory::all();
            $resp->message = 'Tipo de Característica de Categoría borrada exitosamente.';
            return response()->json($resp, 200);
        } else {
            $resp = new \stdClass();
            $resp->state = false;
            $resp->message = 'Tipo de Característica de Categoría no exíste.';
            return response()->json($resp, 200);
        }
    }

    public function allTypeCharacteristicCategory(Request $request)
    {
        $resp = new \stdClass();
        $resp->state = true;
        $resp->message = 'Tipo de Característica de Categoría obtenidas';
        $resp->charactCategorys = TypeCharacteristicCategory::with(['charactCategory', 'charactCategory.category'])->get();
        return response()->json($resp, 200);
    }

    public function allTypeCharacteristicCategoryOfCategory(Request $request, $characteristic_categories_id)
    {
        $resp = new \stdClass();
        $resp->state = true;
        $resp->message = 'Tipo de Característica de Categoría  obtenidas';
        $resp->charactCategorys = Category::find($characteristic_categories_id)->charactCategorys;
        return response()->json($resp, 200);
    }
    /** End - TypeCharacteristicCategory */

    /** Start - ImageSide */
    public function allImageSide(Request $request)
    {
        $resp = new \stdClass();
        $resp->state = true;
        $resp->message = 'Tipo de Posiciones de Imágenes';
        $resp->imageSides = ImageSide::all();
        return response()->json($resp, 200);
    }
    /** End - ImageSide */

    /** Start - ImageType */
    public function createImageType(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'description' => 'required|string|between:2,100',
            'extra_price' => 'nullable|integer',
            'image_side_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $resp = new \stdClass();
            $resp->errors = $validator->errors();
            $resp->state = false;
            $resp->message = 'Ha ocurrido un error.';
            return response()->json($resp, 200);
        }

        $chrtCat = ImageType::create(array_merge(
            $validator->validated()
        ));


        $resp = new \stdClass();
        $resp->message = 'Tipo de Imagen exitosamente creada';
        $resp->imageTypes = ImageType::all();
        $resp->state = true;
        return response()->json($resp, 201);
    }

    public function editImageType(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'name' => 'required|string|between:2,100',
            'description' => 'required|string|between:2,100',
            'extra_price' => 'nullable|integer',
            'image_side_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $resp = new \stdClass();
            $resp->errors = $validator->errors();
            $resp->state = false;
            $resp->message = 'Ha ocurrido un error.';
            return response()->json($resp, 200);
        }

        $imgType = ImageType::find($request->id);

        if ($imgType->name != $request->name) {
            $imgType->name = $request->name;
        }

        if ($imgType->description != $request->description) {
            $imgType->description = $request->description;
        }

        if ($imgType->extra_price != $request->extra_price) {
            $imgType->extra_price = $request->extra_price;
        }

        if ($imgType->image_side_id != $request->image_side_id) {
            $imgType->image_side_id = $request->image_side_id;
        }

        $imgType->save();

        $resp = new \stdClass();
        $resp->message = 'Tipo de Imagen exitosamente editada';
        $resp->imageTypes = ImageType::with(['side'])->get();
        $resp->state = true;
        return response()->json($resp, 201);
    }

    public function deleteImageType(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $resp = new \stdClass();
            $resp->errors = $validator->errors();
            $resp->state = false;
            $resp->message = 'Ha ocurrido un error.';
            return response()->json($resp, 200);
        }

        $imgType = ImageType::find($request->id);
        if ($imgType != null) {
            $imgType->delete();
            $resp = new \stdClass();
            $resp->state = true;
            $resp->imageTypes = ImageType::all()->load(['side']);
            $resp->message = 'Tipo de Imagen borrada exitosamente.';
            return response()->json($resp, 200);
        } else {
            $resp = new \stdClass();
            $resp->state = false;
            $resp->message = 'Tipo de Imagen no exíste.';
            return response()->json($resp, 200);
        }
    }

    public function allImageType(Request $request)
    {
        $resp = new \stdClass();
        $resp->state = true;
        $resp->message = 'Tipo de Imagen obtenidas';
        $resp->imageTypes = ImageType::all()->load(['side']);
        return response()->json($resp, 200);
    }
    /** End - ImageType */
}

