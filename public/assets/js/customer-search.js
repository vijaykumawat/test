console.log("customer-search.js loaded");

$(document).ready(function () {

    console.log("Document Ready");

    $(document).on("keyup", "#customerSearch", function () {

        console.log("Typing...", $(this).val());

    });

});

$(document).ready(function () {

    $(document).on("keyup", "#customerSearch", function () {

        let keyword = $(this).val();

        if (keyword.length < 2) {
            $("#suggestionBox").hide();
            return;
        }

        $.ajax({
            url: searchCustomerUrl,
            type: "GET",
            dataType: "json",
            data: {
                keyword: keyword
            },
            success: function (data) {

                let html = "";

                data.forEach(function (row) {

                    html += `
                        <a href="${baseUrl}/admin/edit-policy-view/${row.policy_id}"
                           class="list-group-item list-group-item-action">
                            <strong>${row.holder_name}</strong><br>
                            <small>
                                ${row.policy_number} |
                                ${row.vehicle_number} |
                                ${row.mobileNo}
                            </small>
                        </a>`;
                });

                $("#suggestionBox").html(html).show();
            }
        });

    });

});

$(document).on("click",".suggestion",function(e){
    e.preventDefault();
    $("#customerSearch").val($(this).data("name"));
    $("#suggestionBox").hide();
});