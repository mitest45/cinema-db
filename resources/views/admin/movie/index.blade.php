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
                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>{{_l('poster')}}</th>
                                <th>{{_l('title')}}</th>
                                <th>{{_l('release_date')}}</th>
                                <th>{{_l('language')}}</th>
                                <th>{{_l('status')}}</th>
                                <th>{{_l('action')}}</th>
                            </tr>
                        </thead>
                        <tbody id="table-tbody">
                            @include(VIEW_PATH.'movie.index-table-tbody')
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
<x-admin.footer />