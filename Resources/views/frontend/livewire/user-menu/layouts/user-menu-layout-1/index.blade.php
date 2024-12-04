<div wire:init="reload(window.location)" id="{{ $ident }}" class="d-inline-block" data-testId="user-menu-layout-1">
    <!--- LOGIN -->
    @if($user)
        @php
            $userData = $user['data'];
        @endphp
        <div  class="account-menu dropdown d-inline-block user-dropdown" id="accMenuDrop">
            <button class="btn dropdown-toggle d-flex align-items-center {{$classUser}}" type="button" role="button"
                    id="dropdownProfile" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false" aria-label="dropdown profile"
                    data-testId="button-menu-dropdown-sign-in">

                @if($typeContent == "0" || $typeContent == "2")
                    <span class="username text-truncate d-none d-sm-block align-middle text-capitalize">
                            <?php if (isset($userData->firstName) && $userData->firstName): ?>
                            <?= $userData->firstName; ?>
                        <?php else: ?>
                                <em>{{trans('core::core.general.complete your profile')}}.</em>
                            <?php endif; ?>
                    </span>
                @endif

                @if($typeContent == "0" || $typeContent == "1")
                    <i class="fa fa-user pl-1" aria-hidden="true"></i>
                @endif
            </button>
            <div id="drop-menu" class="dropdown-menu dropdown-menu-right" >
                <div class="dropdown-item text-center py-3 ">
                    <!-- Nombre -->
                    @if($userData->mainImage)
                        <img class="i-circle d-inline-block rounded-circle border border-dark" style="width: 20px;"
                             src="{{$userData->mainImage}}"/>
                    @else
                        <img class="i-circle d-inline-block rounded-circle border border-dark" style="width: 20px;"
                             src="{{url('modules/iprofile/img/default.jpg')}}"/>
                    @endif

                    <span class="username text-truncate aling-middle text-capitalize">
                    <?php if (isset($userData->firstName) && $userData->firstName): ?>
                            <?= $userData->firstName; ?>
                        <?php else: ?>
                        <em>{{trans('core::core.general.complete your profile')}}.</em>
                    <?php endif; ?>
                    </span>
                </div>
                <a class="dropdown-item"  href="{{$profileRoute}}" data-testId="user-menu-dropdown">
                    <i class="fa fa-user mr-2"></i> {{trans('iprofile::frontend.title.profile')}}
                </a>
                @foreach($moduleLinks as $link)
                    <a class="dropdown-item"  href="{{ $link['url'] }}"  data-testId="menu-dropdown-{{$link['title']}}">
                        @if($link['icon'])<i class="{{ $link['icon'] }}"></i>@endif {{ trans($link['title']) }}
                    </a>
                @endforeach
                <a class="dropdown-item cursor-pointer" wire:click="logout" data-placement="bottom"
                   title="Sign Out" data-testId="menu-dropdown-logout">
                    <i class="fas fa-sign-out-alt mr-1"></i>
                    <span>{{trans('iprofile::frontend.button.sign_out')}}</span>
                </a>
            </div>

        </div>
    @else
        <div class="account-menu dropdown d-inline-block guest-dropdown" id="accMenuDrop">
            <button class="btn dropdown-toggle {{$classUser}}" type="button" role="button"
                    id="dropdownProfile" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false" aria-label="dropdown profile"
                    data-testId="button-menu-dropdown-sign-in">
                    <div class="user d-flex align-items-center">
                        @if($typeContent == "0" || $typeContent == "2")
                            <span class="username text-truncate align-middle text-capitalize">
                                {{ $label }}
                            </span>
                        @endif

                        @if($typeContent == "0" || $typeContent == "1")
                            <i class="fa fa-user pl-1" aria-hidden="true"></i>
                        @endif
                    </div>
            </button>
            <div id="drop-menu" class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownUser">
                @foreach($moduleLinksWithoutSession as $link)
                    <a class="dropdown-item"  href="{{$link['url']}}" {{isset($link["dispatchModal"]) ? "data-toggle=modal data-target=".$link['dispatchModal'] : ''}}
                    data-testId="menu-dropdown-{{$link['title']}}">
                        @if($link['icon'])<i class="{{ $link['icon'] }}"></i>@endif {{ trans($link['title']) }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    @if($openLoginInModal)
        <!-- User login modal -->
        <div class="modal fade" id="userLoginModal" tabindex="-1" aria-labelledby="userLoginModalLabel" aria-hidden="true"
             data-testId="modal-login">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="userLoginModalLabel">{{ trans('user::auth.login') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @include('iprofile::frontend.widgets.login',["embedded" => true, "register" => false])
                    </div>
                </div>
            </div>
        </div>
        <script>
            @if(Session::has('error'))
            $(function(){
                $('#userLoginModal').modal('show');
            })
            @endif
        </script>
    @endif

    @if($openRegisterInModal)
        <!-- User register modal -->
        <div class="modal fade" id="userRegisterModal" tabindex="-1" aria-labelledby="userRegisterModalLabel" aria-hidden="true"
             data-testId="modal-register">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="userRegisterModalLabel">{{ trans('user::auth.register') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @include('iprofile::frontend.widgets.register',["embedded" => true])
                    </div>
                </div>
            </div>
        </div>
    @endif

    @section('scripts')
        <style>
        @if(!empty($styleUser))
        #{{ $ident }} #accMenuDrop > button {
        {!!$styleUser!!}
        }
        @endif
        </style>
        <script type="text/javascript">
            $("#accMenuDrop").hover(function(){
                $(this).addClass("show");
                $('#drop-menu').addClass("show");
            }, function(){
                $(this).removeClass("show");
                $('#drop-menu').removeClass("show");
            });
        </script>
        @parent
    @endsection
</div>