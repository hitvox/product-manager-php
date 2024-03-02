<?php

namespace app\controller;

use app\core\Controller;

class ApiController extends Controller
{
    public function products_index() {
        $productModel = new \app\model\ProductModel();

        $itemsPerPage = isset($_GET['items']) ? $_GET['items'] : 9;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;

        $products = $productModel->getAll($itemsPerPage, $page);

        if(empty($products)){
            echo json_encode([
                'status' => false,
                'message' => 'Nenhum registro encontrado',
            ]); exit();
        }

        echo json_encode([
            'status' => true,
            'data' => $products
        ]);
    }

    public function products_show($id) {
        $productModel = new \app\model\ProductModel();

        $product = $productModel->getById($id);

        if(empty($product)){
            echo json_encode([
                'status' => false,
                'message' => 'Nenhum registro encontrado',
            ]); exit();
        }

        $data = array(
            'status' => true,
            'data' => $product
        );
        echo json_encode($data);
    }

    public function products_store() {
        session_start();

        $category_id = trim($_POST['category']);
        $title = trim($_POST['title']);
        $product_format = trim($_POST['product_format']);
        $sku = trim($_POST['sku']);
        $description = trim($_POST['description']);
        $price = trim($_POST['price']);
        $stock = trim($_POST['stock']);
        $var_colors = $_POST['var_colors'];
        $var_sizes = $_POST['var_sizes'];
        $var_color = $_POST['var_color'];
        $var_size = $_POST['var_size'];
        $var_sku = $_POST['var_sku'];
        $var_price = $_POST['var_price'];
        $var_stock = $_POST['var_stock'];
        
        $error_msg = array();

        // Validation
        if(empty($title)){
            $error_msg[] = 'O campo Título é obrigatório';
        }
        if(empty($product_format)){
            $error_msg[] = 'O campo Formato é obrigatório';
        }
        if($product_format == 'simple' && empty($sku)){
            $error_msg[] = 'O campo SKU é obrigatório';
        }
        if(empty($description)){
            $error_msg[] = 'O campo Descrição é obrigatório';
        }
        if($product_format == 'simple' && empty($price)){
            $error_msg[] = 'O campo Preço é obrigatório';
        }
        if($product_format == 'simple' && empty($stock)){
            $error_msg[] = 'O campo Quantidade é obrigatório';
        }
        if($product_format == 'variation' && (empty($var_colors) || empty($var_sizes))){
            $error_msg[] = 'Selecione ao menos uma Variação';            
        }
        if($product_format == 'variation' && empty($var_price)){
            $error_msg[] = 'Adicione ao menos uma Variação';
        }

        // Validation detection
        if(!empty($error_msg)) {
            $result = array(
                'status' => false,
                'message' => $error_msg[0]
            );
            echo json_encode($result);exit();
        }

        // Insert
        $productModel = new \app\model\ProductModel();

        $price_decimal = floatval(str_replace(',', '.', str_replace('.', '', $price)));

        $params = [
            'category_id'          => !empty($category_id) ? $category_id : null,
            'product_format'       => $product_format,
            'title'                => $title,
            'description'          => $description,
            'sku'                  => ($product_format == 'simple') ? $sku : null,
            'price'                => ($product_format == 'simple') ? $price_decimal : null,
            'stock'                => ($product_format == 'simple') ? $stock : null,
            'variations_colors'    => ($product_format == 'variation') ? json_encode($var_colors) : null,
            'variations_sizes'     => ($product_format == 'variation') ? json_encode($var_sizes) : null
        ];

        $product_id = $productModel->insert((object) $params);

        if(!$product_id) {
            $result = array(
                'status' => false,
                'message' => 'Ocorreu um erro ao adicionar o produto'
            );
            echo json_encode($result);exit();
        }

        if($product_format == 'variation' && $product_id) {
            foreach($var_price as $key => $value) {
                if(empty(trim($var_price[$key])) || empty(trim($var_size[$key]))) {
                    continue;
                }

                $price_decimal_var = floatval(str_replace(',', '.', str_replace('.', '', $var_price[$key])));

                $params_variation = [
                    'product_id'           => $product_id,
                    'color'                => trim($var_color[$key]),
                    'size'                 => trim($var_size[$key]),
                    'sku'                  => trim($var_sku[$key]) ? trim($var_sku[$key]) : null,
                    'price'                => trim($var_price[$key]) ? $price_decimal_var : 0,
                    'stock'                => intval(trim($var_stock[$key])) > 0 ? intval(trim($var_stock[$key])) : 0
                ];
            
                $productModel->insertVariation((object) $params_variation);
            }
        }

        // Images
        $images = $_FILES['images']; // array
        if(!empty($images) && $product_id) {
            $dir = "./images/products/{$product_id}";
            if(!file_exists($dir)) mkdir($dir, 0777, true);
            
            // Upload image to server
            foreach($images['name'] as $key => $image) {
                $ext = pathinfo($image, PATHINFO_EXTENSION);
                $ext = strtolower($ext);

                $extensions_allowed = array('gif', 'jpg', 'jpeg', 'png');

                if(!in_array($ext, $extensions_allowed)) continue;
                
                $filename = uniqid() . '.' . $ext;
                move_uploaded_file($images['tmp_name'][$key], $dir . '/' . $filename);

                // Insert image
                $params_image = [
                    'product_id' => $product_id,
                    'image' => $filename,
                    'is_default' => $key == 0 ? 1 : null
                ];
                $productModel->insertImage((object) $params_image);
            }

        }

        $_SESSION['message'] = 'O produto foi adicionado com sucesso';
        $result = array(
            'status' => true
        );
        echo json_encode($result);
    }

    public function products_update($id) {
        session_start();

        $product_id = $id;

        $category_id = trim($_POST['category']);
        $title = trim($_POST['title']);
        $product_format = trim($_POST['product_format']);
        $sku = trim($_POST['sku']);
        $description = trim($_POST['description']);
        $price = trim($_POST['price']);
        $stock = trim($_POST['stock']);
        $var_colors = $_POST['var_colors'];
        $var_sizes = $_POST['var_sizes'];
        $var_color = $_POST['var_color'];
        $var_size = $_POST['var_size'];
        $var_sku = $_POST['var_sku'];
        $var_price = $_POST['var_price'];
        $var_stock = $_POST['var_stock'];

        $images_prev_removed = trim($_POST['images_prev_removed']);
        
        $error_msg = array();

        // Validation
        if(empty($title)){
            $error_msg[] = 'O campo Título é obrigatório';
        }
        if(empty($product_format)){
            $error_msg[] = 'O campo Formato é obrigatório';
        }
        if($product_format == 'simple' && empty($sku)){
            $error_msg[] = 'O campo SKU é obrigatório';
        }
        if(empty($description)){
            $error_msg[] = 'O campo Descrição é obrigatório';
        }
        if($product_format == 'simple' && empty($price)){
            $error_msg[] = 'O campo Preço é obrigatório';
        }
        if($product_format == 'simple' && empty($stock)){
            $error_msg[] = 'O campo Quantidade é obrigatório';
        }
        if($product_format == 'variation' && (empty($var_colors) || empty($var_sizes))){
            $error_msg[] = 'Selecione ao menos uma Variação';            
        }
        if($product_format == 'variation' && empty($var_price)){
            $error_msg[] = 'Adicione ao menos uma Variação';
        }

        // Validation detection
        if(!empty($error_msg)) {
            $result = array(
                'status' => false,
                'message' => $error_msg[0]
            );
            echo json_encode($result);exit();
        }

        // Insert
        $productModel = new \app\model\ProductModel();

        $price_decimal = floatval(str_replace(',', '.', str_replace('.', '', $price)));

        $params = [
            'id'                   => $id,
            'category_id'          => !empty($category_id) ? $category_id : null,
            'product_format'       => $product_format,
            'title'                => $title,
            'description'          => $description,
            'sku'                  => ($product_format == 'simple') ? $sku : null,
            'price'                => ($product_format == 'simple') ? $price_decimal : null,
            'stock'                => ($product_format == 'simple') ? $stock : null,
            'variations_colors'    => ($product_format == 'variation') ? json_encode($var_colors) : null,
            'variations_sizes'     => ($product_format == 'variation') ? json_encode($var_sizes) : null
        ];

        $productModel->update((object) $params);

        if(!$product_id) {
            $result = array(
                'status' => false,
                'message' => 'Ocorreu um erro ao atualizar o produto'
            );
            echo json_encode($result); exit();
        }

        if($product_format == 'variation' && $product_id) {

            $productModel->deleteVariations((object) [
                'product_id' => $product_id
            ]);

            foreach($var_price as $key => $value) {
                if(empty(trim($var_price[$key])) || empty(trim($var_size[$key]))) {
                    continue;
                }

                $price_decimal_var = floatval(str_replace(',', '.', str_replace('.', '', $var_price[$key])));

                $params_variation = [
                    'product_id'           => $product_id,
                    'color'                => trim($var_color[$key]),
                    'size'                 => trim($var_size[$key]),
                    'sku'                  => trim($var_sku[$key]) ? trim($var_sku[$key]) : null,
                    'price'                => trim($var_price[$key]) ? $price_decimal_var : 0,
                    'stock'                => intval(trim($var_stock[$key])) > 0 ? intval(trim($var_stock[$key])) : 0
                ];
            
                $productModel->insertVariation((object) $params_variation);
            }
        }

        // Images
        if(!empty($images_prev_removed)){
            $images_prev_removed = explode(',', $images_prev_removed);

            foreach($images_prev_removed as $image_remove_id){
                $dir = "./images/products/{$product_id}";
                if(file_exists("{$dir}/{$image_remove_id}")) unlink("{$dir}/{$image_remove_id}");

                $productModel->deleteImage((object) [
                    'image_id' => $image_remove_id,
                    'product_id' => $product_id
                ]);
            }
        }

        $images = $_FILES['images']; // array
        if(!empty($images) && $product_id) {
            $dir = "./images/products/{$product_id}";
            if(!file_exists($dir)) mkdir($dir, 0777, true);
            
            // Upload image to server
            foreach($images['name'] as $key => $image) {
                $ext = pathinfo($image, PATHINFO_EXTENSION);
                $ext = strtolower($ext);

                $extensions_allowed = array('gif', 'jpg', 'jpeg', 'png');

                if(!in_array($ext, $extensions_allowed)) continue;
                
                $filename = uniqid() . '.' . $ext;
                move_uploaded_file($images['tmp_name'][$key], $dir . '/' . $filename);

                // Insert image
                $params_image = [
                    'product_id' => $product_id,
                    'image' => $filename,
                    'is_default' => $key == 0 ? 1 : null
                ];
                $productModel->insertImage((object) $params_image);
            }

        }

        $_SESSION['message'] = 'O produto foi editado com sucesso';
        $result = array(
            'status' => true
        );
        echo json_encode($result);
    }

    public function products_delete($id) {
        $productModel = new \app\model\ProductModel();

        // Remove images
        $dir = "./images/products/{$id}";
        if(file_exists($dir)) rmdir($dir);

        $productModel->delete((object) [
            'id' => $id
        ]);

        echo json_encode([
            'status' => true
        ]);
    }

    public function categories_index()
    {
        $productModel = new \app\model\ProductModel();

        $categories = $productModel->getCategories();

        if(!$categories) {
            echo json_encode([
                'status' => false,
                'message' => 'Nenhuma categoria encontrada'
            ]); exit();
        }

        $data = array(
            'status' => true,
            'data' => $categories
        );
        echo json_encode($data);
    }

    public function categories_store()
    {
        session_start();

        if(empty(trim($_POST['title']))) {
            echo json_encode([
                'status' => false,
                'message' => 'O campo título é obrigatório'
            ]); exit();
        }

        $productModel = new \app\model\ProductModel();
        
        $params = [
            'title' => trim($_POST['title'])
        ];

        $productModel->insertCategory((object) $params);

        $_SESSION['message'] = 'A categoria foi criada com sucesso';

        echo json_encode([
            'status' => true            
        ]);
    }

    public function categories_delete($id)
    {
        $productModel = new \app\model\ProductModel();

        $productModel->deleteCategory((object) [
            'id' => $id
        ]);

        echo json_encode([
            'status' => true            
        ]);
    }
}