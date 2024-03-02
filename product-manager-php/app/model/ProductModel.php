<?php

namespace app\model;

use app\core\Model;

class ProductModel
{
    private $pdo;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->pdo = new Model();
    }

    /**
     * @return array[object]
     */
    public function getAll($itemsPerPage = 25, $page = null)
    {
            $page = $page ?? 1;
            $offset = ($page - 1) * $itemsPerPage;

        $sql = "SELECT a.id, a.product_format, a.title, a.price, b.image FROM products AS a
                LEFT JOIN products_images AS b ON a.id = b.product_id AND b.is_default = 1
                ORDER BY a.id DESC
                LIMIT $itemsPerPage OFFSET $offset";

        $dt = $this->pdo->executeQuery($sql);

        $total_items = count($this->pdo->executeQuery("SELECT id FROM products"));
        $total_pages = ceil($total_items / $itemsPerPage);

        $listProduct = array();

        foreach ($dt as $dr)
            $listProduct[] = $this->collection_products($dr);

        $data = array(
            'total_items' => $total_items,
            'total_pages' => $total_pages,
            'items' => $listProduct
        );

        return $data;
    }


    /**
     * @param  int $id ID of product
     * @return object Return a object with product data
     */
    public function getById(int $id)
    {
        $sql = "SELECT id, category_id, product_format, title, description, sku, price, stock, variations_colors, variations_sizes FROM products
                WHERE id = :id";

        $param = [
            ':id' => $id
        ];

        $dr = $this->pdo->executeQueryOneRow($sql, $param);

        if(!$dr) return false;

        $info = $this->collection_product($dr);

        if($info->category_id) {
            $category = $this->getCategory($info->category_id);

            $info->category = $category->title;
        }else{
            $info->category = null;
        }

        $sql = "SELECT color, size, sku, price, stock FROM products_variations WHERE product_id = :id";

        $dr = $this->pdo->executeQuery($sql, $param);

        $variations = [];

        foreach ($dr as $dr)
            $variations[] = $this->collection_variations($dr);

        $sql = "SELECT id, image, is_default FROM products_images WHERE product_id = :id";

        $dr = $this->pdo->executeQuery($sql, $param);

        $images = [];

        foreach ($dr as $dr)
            $images[] = $this->collection_images($dr);

        return array(
            'info' => $info,
            'variations' => $variations,
            'images' => $images
        );
    }


    /**
     * @param  Object $params List with parameters to be inserted
     * @return int Retorn product ID or false on error
     */
    public function insert(object $params)
    {
        $sql = 'INSERT INTO products (category_id, product_format, title, description, sku, price, stock, variations_colors, variations_sizes, created_at, updated_at) VALUES (:category_id, :product_format, :title, :description, :sku, :price, :stock, :variations_colors, :variations_sizes, NOW(), NOW())';

        $params = [
            ':category_id'          => $params->category_id,
            ':product_format'       => $params->product_format,
            ':title'                => $params->title,
            ':description'          => $params->description,
            ':sku'                  => $params->sku,
            ':price'                => $params->price,
            ':stock'                => $params->stock,
            ':variations_colors'    => $params->variations_colors,
            ':variations_sizes'     => $params->variations_sizes
        ];

        if(!$this->pdo->executeNonQuery($sql, $params)) return false;

        return $this->pdo->getLastID();
    }

    /**
     * @param  Object $params List with parameters to be inserted
     * @return bool True in case of success or false on error
     */
    public function insertVariation(object $params)
    {
        $sql = 'INSERT INTO products_variations (product_id, color, size, sku, price, stock, created_at, updated_at) VALUES (:product_id, :color, :size, :sku, :price, :stock, NOW(), NOW())';

        $params = [
            ':product_id'           => $params->product_id,
            ':color'                => $params->color,
            ':size'                 => $params->size,
            ':sku'                  => $params->sku,
            ':price'                => $params->price,
            ':stock'                => $params->stock            
        ];

        if(!$this->pdo->executeNonQuery($sql, $params)) return false;

        return true;
    }

    /**
     * @param  Object $params List with parameters to be inserted
     * @return bool True in case of success or false on error
     */
    public function insertImage(object $params) {
        $sql = 'INSERT INTO products_images (product_id, image, is_default, created_at, updated_at) VALUES (:product_id, :image, :is_default, NOW(), NOW())';

        $params = [
            ':product_id'           => $params->product_id,
            ':image'                => $params->image,
            ':is_default'           => $params->is_default
        ];

        if(!$this->pdo->executeNonQuery($sql, $params)) return false;

        return true;
    }

    /**
     * @param  Object $params List with parameters to be updated
     * @return bool True in case of success or false on error
     */
    public function update(object $params)
    {
        $sql = 'UPDATE products SET category_id = :category_id, product_format = :product_format, title = :title, description = :description, sku = :sku, price = :price, stock = :stock, variations_colors = :variations_colors, variations_sizes = :variations_sizes, updated_at = NOW()
                WHERE id = :id';

        $params = [
            ':id'                   => $params->id,
            ':category_id'          => $params->category_id,
            ':product_format'       => $params->product_format,
            ':title'                => $params->title,
            ':description'          => $params->description,
            ':sku'                  => $params->sku,
            ':price'                => $params->price,
            ':stock'                => $params->stock,
            ':variations_colors'    => $params->variations_colors,
            ':variations_sizes'     => $params->variations_sizes
        ];

        if(!$this->pdo->executeNonQuery($sql, $params)) return false;

        return true;
    }

    public function deleteVariations(object $params)
    {
        $sql = 'DELETE FROM products_variations
                WHERE product_id = :product_id';

        $params = [
            ':product_id' => $params->product_id
        ];
        
        if(!$this->pdo->executeNonQuery($sql, $params)) return false;

        return true;
    }

    public function deleteImage(object $params)
    {
        $sql = 'DELETE FROM products_images
                WHERE id = :image_id AND product_id = :product_id';

        $params = [
            ':image_id'             => $params->image_id,
            ':product_id'           => $params->product_id
        ];
        
        if(!$this->pdo->executeNonQuery($sql, $params)) return false;

        return true;
    }
    
    public function delete(object $params)
    {
        $sql = 'DELETE FROM products
                WHERE id = :id';
        
        $params = [
            ':id' => $params->id
        ];

        if(!$this->pdo->executeNonQuery($sql, $params)) return false;
        
        return true;        
    }

    public function getCategories()
    {
        $sql = 'SELECT id, title FROM categories';
        
        $dr = $this->pdo->executeQuery($sql);

        if(!$dr) return [];

        $categories = [];

        foreach ($dr as $dr)
            $categories[] = $this->collection_categories($dr);

        return $categories;
    }

    public function getCategory(int $id)
    {
        $sql = 'SELECT id, title FROM categories
                WHERE id = :id';

        $param = [
            ':id' => $id
        ];

        if(!$dr = $this->pdo->executeQueryOneRow($sql, $param)) return false;

        return $this->collection_categories($dr);
    }

    public function insertCategory(object $params)
    {
        $sql = 'INSERT INTO categories (title, created_at, updated_at) VALUES (:title, NOW(), NOW())';

        $params = [
            ':title' => $params->title
        ];

        if(!$this->pdo->executeNonQuery($sql, $params)) return false;

        return true;
    }

    public function deleteCategory(object $params)
    {
        $sql = 'DELETE FROM categories
                WHERE id = :id';
        
        $params = [
            ':id' => $params->id
        ];

        if(!$this->pdo->executeNonQuery($sql, $params)) return false;

        return true;
    }

    private function collection_categories($param)
    {
        return (object)[
            'id'     => $param['id'] ?? null,
            'title'  => $param['title'] ?? null
        ];
    }

    /**
     * @param  array|object $param Receive the parameters to be converted
     * @return object Return a object with products data
     */
    private function collection_products($param)
    {
        return (object)[
            'id'                 => $param['id'] ?? null,
            'product_format'     => $param['product_format'] ?? null,
            'title'              => $param['title'] ?? null,
            'price'              => $param['price'] ? number_format($param['price'], 2, ',', '.') : null,
            'image'              => $param['image'] ?? null
        ];
    }

    /**
     * @param  array|object $param Receive the parameters to be converted
     * @return object Return a object with product data
     */
    private function collection_product($param)
    {
        return (object)[
            'id'                 => $param['id'] ?? null,
            'category_id'        => $param['category_id'] ?? null,
            'product_format'     => $param['product_format'] ?? null,
            'title'              => $param['title'] ?? null,
            'description'        => $param['description'] ?? null,
            'sku'                => $param['sku'] ?? null,
            'price'              => $param['price'] ? number_format($param['price'], 2, ',', '.') : null,
            'stock'              => $param['stock'] ?? null,
            'variations_colors' =>  $param['variations_colors'] ? json_decode($param['variations_colors']) : null,
            'variations_sizes'  =>  $param['variations_sizes'] ? json_decode($param['variations_sizes']) : null
        ];
    }

    /**
     * @param  array|object $param Receive the parameters to be converted
     * @return object Return a object with variations data
     */
    private function collection_variations($param)
    {
        return (object)[
            'color'              => $param['color'] ?? null,
            'size'               => $param['size'] ?? null,
            'sku'                => $param['sku'] ?? null,
            'price'              => $param['price'] ? number_format($param['price'], 2, ',', '.') : null,
            'stock'              => $param['stock'] ?? null
        ];
    }

    /**
     * @param  array|object $param Receive the parameters to be converted
     * @return object Return a object with images data
     */
    private function collection_images($param)
    {
        return (object)[
            'id'              => $param['id'] ?? null,
            'image'              => $param['image'] ?? null,
            'is_default'         => $param['is_default'] ?? null
        ];
    }
}