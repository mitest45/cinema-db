<x-admin.header />

<div class="container-fluid p-0">
    <div class="d-flex justify-content-between">
        <div class="heading-div">
            <h1 class="h3 mb-3">{{_l('movie')}}</h1>
        </div>
        <div class="buttons-div">
            <a href="{{route('admin.movie.add')}}" class="btn btn-primary">{{_l('add_movie')}}</a>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Empty card</h5>
                </div>
                <div class="card-body">

                </div>
            </div>
        </div>
    </div>

</div>

<x-admin.footer />