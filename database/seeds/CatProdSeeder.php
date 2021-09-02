<?php

use App\Brand;
use App\Category;
use App\ImageSide;
use App\ImageType;
use Illuminate\Database\Seeder;

class CatProdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $imageSide = new ImageSide();
        $imageSide->name = 'Centrada';
        $imageSide->description = 'Imagen/Texto Centrada';
        $imageSide->y = 1;
        $imageSide->x = 1;
        $imageSide->save();

        $imageSide = new ImageSide();
        $imageSide->name = 'Centrada Horizontal Superior';
        $imageSide->description = 'Imagen/Texto Centrada Horizontal Superior';
        $imageSide->y = 0;
        $imageSide->x = 1;
        $imageSide->save();

        $imageSide = new ImageSide();
        $imageSide->name = 'Centrada Horizontal Inferior';
        $imageSide->description = 'Imagen/Texto en la Esquina Inferior Izquierda';
        $imageSide->y = 2;
        $imageSide->x = 1;
        $imageSide->save();

        $imageSide = new ImageSide();
        $imageSide->name = 'Centrada Vertical Izquierda';
        $imageSide->description = 'Imagen/Texto Centrada Vertical Izquierda';
        $imageSide->y = 1;
        $imageSide->x = 0;
        $imageSide->save();

        $imageSide = new ImageSide();
        $imageSide->name = 'Centrada Vertical Derecha';
        $imageSide->description = 'Imagen/Texto Centrada Vertical Derecha';
        $imageSide->y = 1;
        $imageSide->x = 2;
        $imageSide->save();

        $imageSide = new ImageSide();
        $imageSide->name = 'Esquina Superior Izquierda';
        $imageSide->description = 'Imagen/Texto en la Esquina Superior Izquierda';
        $imageSide->y = 0;
        $imageSide->x = 0;
        $imageSide->save();

        $imageSide = new ImageSide();
        $imageSide->name = 'Esquina Superior Derecha';
        $imageSide->description = 'Imagen/Texto en la Esquina Superior Derecha';
        $imageSide->y = 0;
        $imageSide->x = 2;
        $imageSide->save();

        $imageSide = new ImageSide();
        $imageSide->name = 'Esquina Inferior Izquierda';
        $imageSide->description = 'Imagen/Texto en la Esquina Inferior Izquierda';
        $imageSide->y = 2;
        $imageSide->x = 0;
        $imageSide->save();

        $imageSide = new ImageSide();
        $imageSide->name = 'Esquina Inferior Derecha';
        $imageSide->description = 'Imagen/Texto en la Esquina Inferior Izquierda';
        $imageSide->y = 2;
        $imageSide->x = 2;
        $imageSide->save();

        $cat = new Category();
        $cat->name = 'Lápices';
        $cat->description = 'Categoría de Lápices';
        $cat->save();

        $cat = new Category();
        $cat->name = 'Cuadernos';
        $cat->description = 'Categoría de Cuardernos';
        $cat->save();

        $cat = new Category();
        $cat->name = 'FlashCards';
        $cat->description = 'Categoría de FlashCards';
        $cat->save();

        $cat = new Category();
        $cat->name = 'Lápices';
        $cat->description = 'Categoría de Lápices';
        $cat->save();

        $brand = new Brand();
        $brand->name = 'ChiniProducto';
        $brand->description = 'Producto creado por Chinipapelería';
        $brand->save();

        $brand = new Brand();
        $brand->name = 'Genérico';
        $brand->description = 'Producto genérico';
        $brand->save();

        $brand = new Brand();
        $brand->name = 'Zebra';
        $brand->description = 'Marca Zebra';
        $brand->save();

        $typeImg = new ImageType();
        $typeImg->name = 'Imagen Portada';
        $typeImg->description = 'Imagen de la Portada';
        $typeImg->image_side_id = 1;
        $typeImg->save();

        $typeImg = new ImageType();
        $typeImg->name = 'Imagen Contraportada';
        $typeImg->description = 'Imagen de la Contraportada';
        $typeImg->image_side_id = 1;
        $typeImg->save();

        $typeImg = new ImageType();
        $typeImg->name = 'Imagen Esquina Inferior Derecha';
        $typeImg->description = 'Imagen en la Esquina Inferior Derecha para FlashCard';
        $typeImg->image_side_id = 9;
        $typeImg->save();
    }
}
