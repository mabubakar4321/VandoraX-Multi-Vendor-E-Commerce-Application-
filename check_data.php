<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
echo 'Categories: '.App\Models\Category::count().PHP_EOL;
foreach(App\Models\Category::all() as $c){ echo $c->id.' | '.$c->name.' | parent:'.($c->parent_id??'NULL').' | active:'.($c->is_active?1:0).PHP_EOL; }
echo '--- Vendors: '.App\Models\Vendor::count().PHP_EOL;
foreach(App\Models\Vendor::with('category')->get() as $v){ echo $v->id.' | '.$v->store_name.' | cat:'.$v->category_id.' | status:'.$v->status.PHP_EOL; }
echo '--- Products: '.App\Models\Product::count().PHP_EOL;
foreach(App\Models\Product::all() as $p){ echo $p->id.' | '.$p->name.' | vendor:'.$p->vendor_id.' | cat:'.$p->category_id.PHP_EOL; }
