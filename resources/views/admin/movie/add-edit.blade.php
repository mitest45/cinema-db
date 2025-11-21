<x-admin.header />

<div class="container-fluid p-0">

    <div class="d-flex justify-content-between">
        <div class="heading-div">
            <h1 class="h3 mb-3">{{ $heading }}</h1>
        </div>
        <div class="buttons-div">
           
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{route('admin.movie.save',optional($movie)->id)}}" method="POST" enctype="multipart/form-data" id="product_form">
                        @csrf
                        @include('admin.movie.form')
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>


<x-admin.footer />
