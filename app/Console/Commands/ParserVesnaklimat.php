<?php
namespace App\Commands;

use Illuminate\Console\Command;
use Goutte\Client;
use Storage;
use DB;

class ParserVesnaklimat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'paresr:vesnaklimat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'parsing';


    protected $url = "https://vesnaklimat.ru";

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
        $product = $this->product();
        echo "\n successful \n";
    }
    protected function product()
    {
       DB::table('product')->where("type","VESNAKLIMAT")->delete();

        $cat_id  = DB::table('category')->where("name","Настенные кондиционеры (сплит-системы)")->first()->id;
       
        $crawler = $this->client->request('GET', $this->url."/nastennie-split-sistemi");

        $product  = $crawler->filter('.container-catalog .one-product')->each(function ($node) use ($cat_id)  {
            $url = $this->url.trim($node->filter('img')->attr("src"));
            $_contents = file_get_contents($url);
            $ImgName = substr($url, strrpos($url, '/') + 1);
            Storage::disk('local')->put("product/".$ImgName,$_contents);
            $name = str_replace("Сплит-система","", $node->filter('.name')->text());
            $brName = explode(' ',trim($name))[0];
            $brand = DB::table('brand')->where("name",$brName)->first();
            if($brand){
                $brandId = $brand->id;
            }else{
                $brandId =  DB::table('brand')->insertGetId(["name"=>$brName]);
            }
            echo ".";
            return [
                "cat_id" => $cat_id,
                "brand_id"  => $brandId,
                "model"  => trim(strstr(trim($name), " ")),
                "image"  => $ImgName,
                "type"   => "VESNAKLIMAT",
                "price"  => trim($node->filter('meta[itemprop="price"]')->attr("content")),
            ];
        });
        DB::table('product')->insertOrIgnore($product);
    }
    
}
