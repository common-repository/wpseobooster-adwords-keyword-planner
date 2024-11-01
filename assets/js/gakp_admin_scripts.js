jQuery(function (t) {
    var e = function (t) {
        return jQuery.post(gakp_admin_ajax_object.ajax_url, t)
    };
    t(document).ready(function () {
        var n;
        t(document).on("click", "#gakp-google-logout", function (t) {
            console.log("hi");
            e({
                action: "gakp_logout_process"
            }).done(function (t) {
                1 == t.data && location.reload()
            })
        }), t("#get-result").click(function (s) {
            if (s.preventDefault(), "" != t("#keyword").val()) {
                var a = {
                    action: "google_keyword",
                    keyword: t("#keyword").val(),
                    language: t("#languages option:selected").val(),
                    country: t("#countries option:selected").val()
                };
                t("#loader").removeClass("hidden"), e(a).done(function (e) {
                    n = e.data, t("#keyword-table").DataTable({
                        data: e.data,
                        destroy: !0,
                        columnDefs: [{
                            targets: 0,
                            data: "keyword",
                            render: function (t, e, n, s) {
                                return '<span class="kwsearch"> ' + t + "</span>"
                            }
                        }, {
                            targets: 1,
                            data: "searchVolume",
                            render: function (t, e, n, s) {
                                return '<span class="avsearches"> ' + t + "</span>"
                            }
                        }, {
                            targets: 2,
                            data: null,
                            render: function (t, e, n, s) {
                                return '<span class="tests"></span> <button class = "view-monthly-data btn btn-primary btn-sm" btn-id="' + s.row + '" data-toggle="modal" data-target="#exampleModal">View Result</button>'
                            }
                        }, {
                            targets: 3,
                            data: "averageCpc",
                            render: function (t, e, n, s) {
                                return '<span class="avcpc"> ' + t + "</span>"
                            }
                        }]
                    }), t("#keyword-table").show(), t("#loader").addClass("hidden")
                })
            }
        }), t(document).on("click", ".view-monthly-data", function (e) {
            e.preventDefault(), t(".modal-body").empty(), t(".modal-title").empty(), id = t(this).attr("btn-id"), monthlyStats = n[id].keyword_stats;
            var s = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
                a = '<table class="table table-striped"><thead><tr><th scope="col">#</th><th scope="col">Year</th><th scope="col">Month</th><th scope="col">Searches</th></tr></thead><tbody>';
            t.each(monthlyStats, function (t, e) {
                a += '<tr> <th scope="row">' + ++t + "</th><td>" + e.year + "</td><td>" + s[e.month - 1] + "</td><td>" + e.count + "</td></tr>"
            }), a += "</tbody></table>", t(".modal-title").append('Statistics of searching "' + n[id].keyword + '" keyword'), t(".modal-body").append(a)
        }), t(".gakp-btn-reset").click(function (e) {
            e.preventDefault(), t("#keyword-table").hide().DataTable().clear().destroy()
        }), t('[data-toggle="tooltip"]').tooltip()
    })
});