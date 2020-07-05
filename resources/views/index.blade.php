@extends('layouts.app')
@section('content')
<div class="row">
  <div class="col-lg-3">
    <h1 class="my-4">Category</h1>
    <div class="list-group">
        @foreach($cat as $ley=> $value)
          <a href="?cat={{$value->id}}" class="list-group-item">{{$value->name}}</a>
        @endforeach
    </div>
  </div>
  <!-- /.col-lg-3 -->
  <div class="col-lg-9">
    <div id="carouselExampleIndicators" class="carousel slide my-4" data-ride="carousel">
      <ol class="carousel-indicators">
        <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
      </ol>
      <div class="carousel-inner" role="listbox">
        <div class="carousel-item active">
          <img class="d-block img-fluid" src="https://previews.123rf.com/images/naypong/naypong1608/naypong160800007/61665473-air-conditioner-on-wall-background.jpg" alt="First slide">
        </div>
      </div>
      <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
      </a>
    </div>
    <div class="row">
        <div class="col-xs-12">
            {{ $praduct->appends(['cat' => request()->cat])->links() }}
        </div>
    </div>    
    <div class="row">
        @foreach($praduct as $ley=> $value)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <a href="#"><img class="card-img-top" src="/uploads/product/{{$value->image}}" alt="{{$value->model}}"></a>
                    <div class="card-body">
                        <h4 class="card-title">
                        <a href="#">{{$value->model}}</a>
                        </h4>
                        <h5>${{$value->price}}</h5>
                        <p class="card-text">{{$value->brand->name}}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="row">
        <div class="col-xs-12">
            {{ $praduct->appends(['cat' => request()->cat])->links() }}
        </div>
    </div>    
    <!-- /.row -->
  </div>
  <!-- /.col-lg-9 -->
</div>
@stop
    @section('scripts')
    <script type="text/javascript"></script>
@stop