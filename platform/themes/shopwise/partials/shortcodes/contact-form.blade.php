<div class="section pb_0">
    <div class="row align-items-center">
        <div class="col-xl-4 col-md-6">
            <div class="contact_wrap contact_style3">
                <div class="contact_icon">
                    <i class="linearicons-envelope-open"></i>
                </div>
                <div class="contact_text">
                    <span>{{ __('Email Address') }}</span>
                    <a href="mailto:{{ theme_option('email') }}">{{ theme_option('email') }}</a>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="contact_wrap contact_style3">
                <div class="contact_icon">
                    <i class="linearicons-map2"></i>
                </div>
                <div class="contact_text">
                    <span>{{ __('Address') }}</span>
                    <p><a href="https://www.google.com/maps/place/{{ theme_option('address') }}" target="_blank">{!! theme_option('address') !!}</a></p>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="contact_wrap contact_style3">
                <div class="contact_icon">
                    <i class="linearicons-tablet2"></i>
                </div>
                <div class="contact_text">
                    <span>{{ __('Phone') }}</span>
                    <p><a href="tel:{{ theme_option('hotline') }}">{{ theme_option('hotline') }}</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="section pt-0">
    <div class="row">
        <div class="col-12">
            <div class="heading_s1">
                <h2>{{ __('Get In touch') }}</h2>
            </div>
            <div class="field_form">
                {!! Form::open(['route' => 'public.send.contact', 'class' => 'form--contact contact-form', 'method' => 'POST']) !!}
                    {!! apply_filters('pre_contact_form', null) !!}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="contact_name" class="form-label required">{{ __('Name') }}</label>
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}" id="contact_name"
                                       placeholder="{{ __('Name') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="contact_email" class="form-label required">{{ __('Email') }}</label>
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}" id="contact_email"
                                       placeholder="{{ __('Email') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="contact_address" class="form-label">{{ __('Address') }}</label>
                                <input type="text" class="form-control" name="address" value="{{ old('address') }}" id="contact_address"
                                       placeholder="{{ __('Address') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="contact_phone" class="form-label">{{ __('Phone') }}</label>
                                <input type="text" class="form-control" name="phone" value="{{ old('phone') }}" id="contact_phone"
                                       placeholder="{{ __('Phone') }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="contact_subject" class="form-label">{{ __('Subject') }}</label>
                                <input type="text" class="form-control" name="subject" value="{{ old('subject') }}" id="contact_subject"
                                       placeholder="{{ __('Subject') }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="contact_content" class="form-label required">{{ __('Message') }}</label>
                                <textarea name="content" id="contact_content" class="form-control" rows="5" placeholder="{{ __('Message') }}">{{ old('content') }}</textarea>
                            </div>
                        </div>
                        @if (is_plugin_active('captcha'))
                            @if (setting('enable_captcha'))
                                <div class="mb-3 col-12">
                                    {!! Captcha::display() !!}
                                </div>
                            @endif

                            @if (setting('enable_math_captcha_for_contact_form', 0))
                                <div class="mb-3 col-12">
                                    <label for="math-group" class="contact-label required">{{ app('math-captcha')->label() }}</label>
                                    {!! app('math-captcha')->input(['class' => 'form-control', 'id' => 'math-group']) !!}
                                </div>
                            @endif
                        @endif

                        {!! apply_filters('after_contact_form', null) !!}

                        <div class="col-md-12">
                            <div class="mb-3">
                                <button type="submit" class="btn btn-fill-out">{{ __('Send Message') }}</button>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="contact-message contact-success-message" style="display: none"></div>
                        <div class="contact-message contact-error-message" style="display: none"></div>
                    </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
<!-- END SECTION CONTACT -->
