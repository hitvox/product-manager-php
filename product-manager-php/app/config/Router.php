<?php

// Web Routes
$this->get('/', 'PagesController@home');
$this->get('/product/{id}', 'PagesController@product');
$this->get('/categories', 'PagesController@categories');

// Api Routes
$this->get('/api/products', 'ApiController@products_index');
$this->get('/api/products/{id}', 'ApiController@products_show');
$this->post('/api/products', 'ApiController@products_store');
$this->post('/api/products/{id}', 'ApiController@products_update');
$this->delete('/api/products/{id}', 'ApiController@products_delete');
$this->get('/api/categories', 'ApiController@categories_index');
$this->post('/api/categories', 'ApiController@categories_store');
$this->delete('/api/category/{id}', 'ApiController@categories_delete');