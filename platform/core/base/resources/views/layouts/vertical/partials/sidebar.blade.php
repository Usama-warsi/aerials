<div class="collapse navbar-collapse" id="sidebar-menu">
    @include('core/base::layouts.partials.navbar-nav', [
        'autoClose' => 'false',
    ])
</div>

<style>
    #cms-core-tools, #cms-core-plugins, #cms-core-theme, #panel-section-item-system-audit-logs, .panel-section-item.panel-section-item-media, .panel-section-item.panel-section-item-website_tracking, .panel-section-item.panel-section-item-common, .panel-section-item.panel-section-item-system_cleanup, div#panel-section-item-system-information, div#panel-section-item-system-cache_management, div#panel-section-item-system-cronjob, .panel-section-item.panel-section-item-social-login, .panel-section-item.panel-section-item-ads, .panel-section-item.panel-section-item-analytics, .panel-section-item.panel-section-item-contact, .panel-section-item.panel-section-item-captcha, .panel-section-item.panel-section-item-faqs, .panel-section-item-priority-170, .panel-section-item-priority-110, .panel-section-item-priority-120, .panel-section-item-priority-100, .panel-section-item-priority-70, .panel-section-item-priority-180, .panel-section-item-priority-160, .panel-section-item-priority-30, .panel-section-item-priority-190, .panel-section-item-priority-60 {
	display: none !important;
    }
    .hide-md-3 {
    	display: none !important;
    }
    .hide-btn-primary {
    	display: none;
    }
</style>
<script>
window.onload = function() {
    var element1 = document.getElementById('cms-core-plugins');
    var element2 = document.getElementById('cms-core-theme');
    var element3 = document.getElementById('panel-section-item-system-audit-logs');
    var element4 = document.getElementById('cms-core-tools');
    if (element1 || element2 || element3 || element4) {
        element1.remove();
        element2.remove();
        element3.remove();
        element4.remove();
    }
};

$(document).ready(function() {
    var currentUrl = window.location.href;
    
    if (currentUrl.includes("admin/ecommerce/taxes/edit")) {
        $('.col-md-3.gap-3.d-flex.flex-column-reverse.flex-md-column.mb-md-0.mb-5').addClass('hide-md-3');
    }

    if (currentUrl.includes("admin/ecommerce/taxes/rules/edit")) {
        $('button.btn.btn-primary').addClass('hide-btn-primary');
    }
});
</script>