<?php
namespace App\Commands;

use Illuminate\Console\Command;
use Goutte\Client;
use Storage;
use DB;

class ParserLaitklimat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'paresr:laitklimat {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'parsing';


    protected $url = "https://laitklimat.ru";

    protected $client = "";
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Client $client){
        parent::__construct();
        $this->client = $client;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Client $client)
    {
        $category = $this->category();
        echo $this->argument('type');
        if($this->argument('type') == "test"){
            $product = $this->product();
        }elseif($this->argument('type') == "full"){
            $product = $this->productFull();
        }
        echo "\n successful \n";
    }
    
    protected function product()
    {
       DB::table('product')->where("type","LAITKLIMAT")->delete();
       $cat =  DB::table('category')->select(["id","slug"])->get();
       $page = 1;
       foreach($cat as $key => $value){
            $crawler = $this->client->request('GET', $this->url.$value->slug);
            $product = $crawler->filter('.mx_prod__main_div')->each(function ($node)  use ($value) {
                echo ".";
                $url = $this->url.str_replace("../","/", $node->filter('.mx_prod_list_main_pic')->attr("src") );
                
                $_contents = file_get_contents($url);
                $name = substr($url, strrpos($url, '/') + 1);
                Storage::disk('local')->put("product/".$name,$_contents);

                $brName = explode(' ',trim($node->filter('.mx_prod_name_h3 a')->text()))[0];
                $brand = DB::table('brand')->where("name",$brName)->first();

                if($brand){
                    $brandId = $brand->id;
                }else{
                    $brandId =  DB::table('brand')->insertGetId(["name"=>$brName]);
                }
                return [
                    "cat_id" => $value->id,
                    "brand_id"  => $brandId,
                    "model"  => trim(strstr($node->filter('.mx_prod_name_h3 a')->text()," ")),
                    "image"  => $name,
                    "price"  => trim($node->filter('.mx_tovar_price_p1 .mx_set_space')->text()),
                ];
            });
            DB::table('product')->insertOrIgnore($product);
       }
    }
    
    protected function productFull()
    {
        DB::table('category')->where("type","LAITKLIMAT")->delete();

       $cat =  DB::table('category')->select(["id","slug"])->get();
       $page = 1;
       foreach($cat as $key => $value){

        $crawler = $this->client->request('GET', $this->url.$value->slug);

        $crawler->filter('.pagination li .mx_page_li')->each(function ($node) use ($value,$crawler) {
            $page = $node->text();
            if($page > 1){
                $crawler = $this->client->request('GET', $this->url.$value->slug."?page=".$page);
            }
            echo $page;

            $product = $crawler->filter('.mx_prod__main_div')->each(function ($node)  use ($value) {
                echo ".";
                $url = $this->url.str_replace("../","/", $node->filter('.mx_prod_list_main_pic')->attr("src") );
                
                $_contents = file_get_contents($url);
                $name = substr($url, strrpos($url, '/') + 1);
                Storage::disk('local')->put("product/".$name,$_contents);

                $brName = explode(' ',trim($node->filter('.mx_prod_name_h3 a')->text()))[0];
                $brand = DB::table('brand')->where("name",$brName)->first();

                if($brand){
                    $brandId = $brand->id;
                }else{
                    $brandId =  DB::table('brand')->insertGetId(["name"=>$brName]);
                }
                return [
                    "cat_id" => $value->id,
                    "brand_id"  => $brandId,
                    "model"  => trim(strstr($node->filter('.mx_prod_name_h3 a')->text()," ")),
                    "image"  => $name,
                    "price"  => trim($node->filter('.mx_tovar_price_p1 .mx_set_space')->text()),
                ];
            });
            DB::table('product')->insertOrIgnore($product);
      
        });
            
       }
    }
    protected function category()
    {                          
        $crawler = $this->client->request('GET', $this->url."/catalog/bitovie");

        $category  = $crawler->filter('.mx_vid_tov_3lev')->each(function ($node)  {
            $url = $this->url.str_replace("../","/",$node->filter('a img')->attr("src"));
            $_contents = file_get_contents($url);
            $name = substr($url, strrpos($url, '/') + 1);
            Storage::disk('local')->put("category/".$name,$_contents);
            return [
                "name" => $node->filter('a span')->text(),
                "img"  => $name,
                "slug" => $node->filter('a')->attr("href"),
            ];
        });
        DB::table('category')->delete();
        DB::table('category')->insert($category);
        return true;
    }
}
