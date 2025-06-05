<script>
    var spinner =
        '<div class="h-100 d-flex align-items-center justify-content-center">' +
        '<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>' +
        "</div>";
        
    // top_category_products_tab("all");
    inhouse_top_brands("all");
    inhouse_top_categories("all");
    top_sellers_products_tab('all');
    // top_brands_products_tab('all');

    $(".top_category_products_tab").click(function () {
        top_category_products_tab($(this).data("target"));
    });

    $(".inhouse_top_brands").click(function () {
        inhouse_top_brands($(this).data("target"));
    });

    $(".inhouse_top_categories").click(function () {
        inhouse_top_categories($(this).data("target"));
    });

    $(".top_sellers_products_tab").click(function () {
        top_sellers_products_tab($(this).data("target"));
    });

    $(".top_brands_products_tab").click(function () {
        top_brands_products_tab($(this).data("target"));
    });

    function top_category_products_tab(interval_type) {
        $("#top-category-products-section").html(spinner);
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            type: "POST",
            url:
                AIZ.data.appUrl +
                "/admin/dashboard/top-category-products-section",
            data: {
                interval_type: interval_type,
            },
            success: function (data) {
                $("#top-category-products-section").html(data);
                AIZ.plugins.slickCarousel();
            },
        });
    }

    function inhouse_top_brands(interval_type) {
        $("#inhouse-top-brands").html(spinner);
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            type: "POST",
            url: AIZ.data.appUrl + "/admin/dashboard/inhouse-top-brands",
            data: {
                interval_type: interval_type,
            },
            success: function (data) {
                $("#inhouse-top-brands").html(data);
            },
        });
    }

    function inhouse_top_categories(interval_type) {
        $("#inhouse-top-categories").html(spinner);
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            type: "POST",
            url: AIZ.data.appUrl + "/admin/dashboard/inhouse-top-categories",
            data: {
                interval_type: interval_type,
            },
            success: function (data) {
                $("#inhouse-top-categories").html(data);
            },
        });
    }

    function top_sellers_products_tab(interval_type) {
        $("#top-sellers-products-section").html(spinner);
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            type: "POST",
            url:
                AIZ.data.appUrl +
                "/admin/dashboard/top-sellers-products-section",
            data: {
                interval_type: interval_type,
            },
            success: function (data) {
                $("#top-sellers-products-section").html(data);
                AIZ.plugins.slickCarousel();
            },
        });
    }

    function top_brands_products_tab(interval_type) {
        $("#top-brands-products-section").html(spinner);
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            type: "POST",
            url:
                AIZ.data.appUrl +
                "/admin/dashboard/top-brands-products-section",
            data: {
                interval_type: interval_type,
            },
            success: function (data) {
                $("#top-brands-products-section").html(data);
                AIZ.plugins.slickCarousel();
            },
        });
    }
</script>