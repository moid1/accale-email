<div class="sub_section">
    <div class="row">
        <div class="col-md-6">
            @include('helpers.form_control', ['type' => 'text', 'name' => 'name', 'value' => $group->name, 'help_class' => 'admin_group', 'rules' => Acelle\Model\AdminGroup::$rules])
        </div>
    </div>
</div>

<div class="">
    <h2><span class="material-symbols-rounded">settings</span> {{ trans('messages.admin_group_options') }}</h2>

    <div class="tabbable">
        <ul class="nav nav-tabs nav-tabs-top nav-underline">
            <li class="nav-item active text-semibold"><a class="nav-link" href="#top-tab1" data-toggle="tab">
                <span class="material-symbols-rounded">person_outline</span> {{ trans('messages.permissions') }}</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="top-tab1">
                @php
                    $count = 0;
                @endphp
                @foreach (Acelle\Model\AdminGroup::allPermissions() as $key => $items)
                    @php
                        $count += 1;
                    @endphp
                    <div class="d-flex py-2 border-bottom {{ $count%2 == 0 ? 'bg-light' : '' }}">
                        <div class="row" style="width: 100%">
                            <div class="col-md-3 text-end text-md-left">
                                <h5 class="text-primary">{{ trans('messages.' . $key) }}
                                    <span class="material-symbols-rounded text-muted2 ps-4">drag_indicator</span>
                                </h5>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    @foreach ($items as $act => $ops)
                                        <div class="col-6 col-md-3">
                                            @include('helpers.form_control', [
                                                'type' => 'select',
                                                'class' => 'numeric',
                                                'name' => 'permissions[' . $key . "_" . $act .']',
                                                'value' => $permissions[$key . "_" . $act],
                                                'label' => trans('messages.' . $act),
                                                'options' => $ops["options"],
                                                'help_class' => 'admin_group',
                                                'rules' => Acelle\Model\AdminGroup::rules()
                                            ])
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</div>

