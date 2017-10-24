@extends('layouts.master')

@section('content')
    <!-- Page Content -->
    <h1 style="display:none;">{{ $category_select->name }} Shirts</h1>
    <div class="container">
        <form>
            <div class="filter-prodcuts" id="filter-prodcuts">
                <span class="filter-icon hidden-xs">
                    <i class="ion-arrow-graph-up-right"></i>
                </span>
                <div class="categories-select" id="categories-select">
                    <select id="select-category" style="width: 150px;"
                            title="select category">
                        <option value="/">All</option>
                        @foreach($category as $item)
                            <option value="{{ $item->url }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="sub-category" class="sub-category hidden-xs">
                    <ul class="sub-category-list">
                        @foreach($subcategory as $item)
                            <li class="sub-category-item" id="{{ $item->url }}">{{ $item->name }}
                                @if ($category_select->url === $item->url)
                                    <span class="unselect"><i class="ion-ios-close-empty"></i></span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="sub-category col-xs-12 visible-xs-block">
                    <ul class="sub-category-list-xs">
                        @foreach($subcategory as $item)
                            <li class="sub-category-item" id="{{ $item->url }}">{{ $item->name }}
                                @if ($category_select->url === $item->url)
                                    <span class="unselect"><i class="ion-ios-close-empty"></i></span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </form>

        <!-- List products -->
        <div class="row">
            <h2 class="title-box margin">{{$pages->total()}} results for “{{ $category_select->name }}”</h2>
            <div class="select-sort-box pull-right">
                <label for="select-sort">Sort by:</label>
                <select style="width: 150px;" id="select-sort"
                        title="select sort">
                    <option value="lowest">Price: Lowest first</option>
                    <option value="highest">Price: Highest first</option>
                    <option value="">Newest</option>
                    <option value="oldest">Oldest</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="list-products">
                @foreach($pages as $item)
                    <div class="col-md-3 col-xs-6">
                        <div class="item-product">
                            <div class="img-feature">
                                <a href="/shop{{ $item->url }}">
                                    <img @if(getImgFromCampaign($item)['back'] != '') class="img-responsive img-front img-flip-front"
                                         @else class="img-responsive" @endif
                                         src="{{getImgFromCampaign($item)['front']}}"
                                         onerror="this.src='{{ $item->image }}'"
                                         alt="{{ $item->title }}"/>
                                    @if(getImgFromCampaign($item)['back'] != '')
                                        <img style="display: none;"
                                             class="img-responsive img-back img-flip-back"
                                             src="{{getImgFromCampaign($item)['back']}}"
                                             alt="{{ $item->title }}"/>
                                    @endif
                                </a>
                            </div>
                            <div class="meta-product text-center">
                                <h3 class="title-product"><a href="/shop{{ $item->url }}">{{ $item->title }}</a>
                                </h3>
                                <ul class="list-inline">
                                    <li class="price-product"><i class="ion-ios-pricetag-outline"></i>
                                        <span>${{ $item->price }}</span></li>
                                    <li class="price-product"><i class="ion-ios-clock-outline"></i>
                                        <span>{{secondsToTime($item->remaining)}}</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="row">
            <div class="paging text-center">
                {!! $pages->appends(request()->input())->links('pagination.default') !!}
            </div>
        </div>
    </div>

    <script>
        var checkParent = '{{property_exists($category_select,'parent_id')}}';
        var categorySel = '';
        var subcategorySel = '';
        if (checkParent) {
            categorySel = '{{$parent_url}}';
            subcategorySel = '{{$category_select->url}}';
        } else {
            categorySel = '{{$category_select->url}}';
        }
        var slideindex = parseInt('{{$slideindex}}');
        var sortval = '{{$sort}}';
    </script>
@stop
