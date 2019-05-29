<!-- Button which will trigger the event/modal showing -->
<button id="openModal" class="btn-link" type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal"></button>
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ session('errors') ? 'Validation error' : ( session('success') ? 'Action performed successfully' : 'Something went wrong') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    @if( session('errors'))
                        <p>{{ session('errors')->first() }}</p>
                    @elseif( session('success'))
                        <p>{{ session('success')[1] }}</p>
                    @else
                        <p>{{ session('message')[1] }}</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>