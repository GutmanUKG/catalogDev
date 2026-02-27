<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) { die(); }


if (isset($_REQUEST["IFRAME"]) && $_REQUEST["IFRAME"] === "Y")
{
    $APPLICATION->RestartBuffer(); 
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <?$APPLICATION->ShowHead(); ?>
        <style>
            html {
                scroll-behavior: smooth;
            }
            body {    
                display: flex;
            }
            .catalog__element {
                padding: 20px;
            }
        </style>
    </head>
    <body>
        <?$APPLICATION->IncludeComponent(
            "sotbit:sotbit.b2bcabinet.notifications",
            "b2bcabinet",
            array()
        );?>
        <section class="catalog__element">
            <?
            CJSCore::Init("sidepanel");
            $APPLICATION->IncludeComponent(
                "bitrix:catalog.element",
                "b2b_new",
                $componentElementParams,
                $component
            );
            ?>
        </section>
        <script>
            // Make links open in top window, not in iframe
            BX.ready(function() {
                const links = Array.prototype.slice.call(document.getElementsByTagName('a'));
                links.forEach(function(link) {
                    if (link.href.indexOf('#') === -1) {
                        link.setAttribute('target','_basket');
                    }
                });
                // Don't touch it PLEASE!! it works good
                let isSended = false;
                window.addEventListener("unload", function (event) {
                    if (!isSended) {
                        window.top.postMessage({name: "reload", payload: JSON.stringify(event.currentTarget.location)});
                        isSended = true;
                    }
                });
            })
        </script>
    </body>
    </html>
    <?
}
else
{
    $APPLICATION->IncludeComponent(
        "bitrix:catalog.element",
        "b2b_new",
        $componentElementParams,
        $component
    );  
}
?>