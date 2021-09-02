<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'middleware' => ['api', 'cors'],
    'prefix' => 'auth'

], function ($router) {
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::get('user-profile', 'AuthController@userProfile');
    Route::post('img-user-profile', 'AuthController@changePhotoProfile');
    Route::post('update-address', 'AuthController@updateAddress');
    Route::post('update-profile', 'AuthController@updateProfile');
});

Route::group([
    'middleware' => ['api', 'cors'],
    'prefix' => 'admin'

], function ($router) {
    Route::post('category/create', 'CatProdController@createCategory');
    Route::post('category/delete', 'CatProdController@deleteCategory');
    Route::post('category/edit', 'CatProdController@editCategory');
    Route::get('category/all', 'CatProdController@allCategory');
    Route::get('category/all/characteristics', 'CatProdController@allCategoryWithCharacteristics');
    Route::get('category/all/characteristics/all', 'CatProdController@allCategoryWithAllCharacteristics');


    Route::post('brand/create', 'CatProdController@createBrand');
    Route::post('brand/delete', 'CatProdController@deleteBrand');
    Route::post('brand/edit', 'CatProdController@editBrand');
    Route::get('brand/all', 'CatProdController@allBrand');

    Route::post('product/create', 'CatProdController@createProduct');
    Route::post('product/delete', 'CatProdController@deleteProduct');
    Route::post('product/edit', 'CatProdController@editProduct');
    Route::get('product/all', 'CatProdController@allProduct');
    Route::post('product/characteristic/save', 'CatProdController@saveCharacteristicProduct');

    Route::post('image-type/create', 'CatProdController@createImageType');
    Route::post('image-type/delete', 'CatProdController@deleteImageType');
    Route::post('image-type/edit', 'CatProdController@editImageType');
    Route::get('image-type/all', 'CatProdController@allImageType');

    Route::get('image-side/all', 'CatProdController@allImageSide');

    Route::post('category/characteristic/create', 'CatProdController@createCharacteristicCategory');
    Route::post('category/characteristic/delete', 'CatProdController@deleteCharacteristicCategory');
    Route::post('category/characteristic/edit', 'CatProdController@editCharacteristicCategory');
    Route::get('category/characteristic/all', 'CatProdController@allCharacteristicCategory');
    Route::get('category/characteristic/all/{category_id}', 'CatProdController@allCharacteristicCategoryOfCategory');

    Route::post('category/characteristic/type/create', 'CatProdController@createTypeCharacteristicCategory');
    Route::post('category/characteristic/type/delete', 'CatProdController@deleteTypeCharacteristicCategory');
    Route::post('category/characteristic/type/edit', 'CatProdController@editTypeCharacteristicCategory');
    Route::get('category/characteristic/type/all', 'CatProdController@allTypeCharacteristicCategory');

});
