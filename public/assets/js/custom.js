    // Force-clear any top padding/margin that runtime scripts may add
    (function() {
        function clearTopSpace() {
            try {
                var sels = ['body', '#main-wrapper', '.page-wrapper', '.body-wrapper', '.body-wrapper-inner',
                    '.app-header', '.navbar', 'html'
                ];
                sels.forEach(function(s) {
                    var el = (s === 'body' || s === 'html') ? (s === 'body' ? document.body : document
                        .documentElement) : document.querySelector(s);
                    if (el) {
                        el.style.paddingTop = '0px';
                        el.style.marginTop = '0px';
                        el.style.top = '0px';
                    }
                });
            } catch (e) {
                console && console.error && console.error(e)
            }
        }
        // run after load and slightly after to override any delayed script
        window.addEventListener('load', function() {
            clearTopSpace();
            setTimeout(clearTopSpace, 250);
            setTimeout(clearTopSpace, 1000);
        });
        document.addEventListener('DOMContentLoaded', clearTopSpace);
    })();
