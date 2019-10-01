<div class="lds-css ng-scope" id="loadingBlock">
    <div style="width:100%;height:100%" class="lds-eclipse"><div></div></div>
</div>

@include('components.modal_sharing')

<div id="sortPopup" class="modal">
    <div class="modal-dialog" style="width: 300px; max-width:500px;">
        <div class="modal-content">
            <div class="modal-header">
                <h1>{{__('Sorting')}}</h1>
            </div>
            <div class="modal-body">
                <form>
                    <input type="hidden" value="{{ csrf_token() }}" name="_token">
                    <label for="sortingSelect" style="{{app()->getLocale() == 'he' ? 'width:90px;' : 'width:105px;'}}">{{__('Sorting by')}}: </label>
                    <select name="sorting" id="sortingSelect">
                        <option @if(Session::has('sort') && Session::get('sort') == 'date_desc') selected @endif value="date_desc">{{__('Upload date des')}}</option>
                        <option @if(Session::has('sort') && Session::get('sort') == 'date_asc') selected @endif value="date_asc">{{__('Upload date asc')}}</option>
                        <option @if(Session::has('sort') && Session::get('sort') == 'type_asc') selected @endif value="type_asc">{{__('Type A-Z')}}</option>
                        <option @if(Session::has('sort') && Session::get('sort') == 'type_desc') selected @endif value="type_desc">{{__('Type Z-A')}}</option>
                        <option @if(Session::has('sort') && Session::get('sort') == 'name_asc') selected @endif value="name_asc">{{__('Name A-Z')}}</option>
                        <option @if(Session::has('sort') && Session::get('sort') == 'name_desc') selected @endif value="name_desc">{{__('Name Z-A')}}</option>
                    </select>
                </form>
            </div>
            <div class="cb"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default close-button">{{__('Close')}}</button>
            </div>
        </div>
    </div>
</div>

