const App = function () {
    const detectOS = function() {
        const platform = window.navigator.platform,
              windowsPlatforms = ['Win32', 'Win64', 'Windows', 'WinCE'],
              customScrollbarsClass = 'custom-scrollbars';

        // Add class if OS is windows
        windowsPlatforms.indexOf(platform) != -1 && document.documentElement.classList.add(customScrollbarsClass);
    };

    // Disable all transitions
    const transitionsDisabled = function() {
        document.body.classList.add('no-transitions');
    };

    // Enable all transitions
    const transitionsEnabled = function() {
        document.body.classList.remove('no-transitions');
    };

    // Sidebar navigation
    const navigationSidebar = function() {

        // Elements
        const navContainerClass = 'nav-sidebar',
              navItemOpenClass = 'nav-item-open',
              navLinkClass = 'nav-link',
              navLinkDisabledClass = 'disabled',
              navSubmenuContainerClass = 'nav-item-submenu',
              navSubmenuClass = 'nav-group-sub',
              navScrollSpyClass = 'nav-scrollspy',
              sidebarNavElement = document.querySelectorAll(`.${navContainerClass}:not(.${navScrollSpyClass})`);

        // Setup
        sidebarNavElement.forEach(function(nav) {
            nav.querySelectorAll(`.${navSubmenuContainerClass} > .${navLinkClass}:not(.${navLinkDisabledClass})`).forEach(function(link) {
                link.addEventListener('click', function(e) {
                    if (e.target.tagName === 'SPAN') return;

                    e.preventDefault();
                    const submenuContainer = link.closest(`.${navSubmenuContainerClass}`);
                    const submenu = link.closest(`.${navSubmenuContainerClass}`).querySelector(`:scope > .${navSubmenuClass}`);

                    // Collapsible
                    if(submenuContainer.classList.contains(navItemOpenClass)) {
                        new bootstrap.Collapse(submenu).hide();
                        submenuContainer.classList.remove(navItemOpenClass);
                    }
                    else {
                        new bootstrap.Collapse(submenu).show();
                        submenuContainer.classList.add(navItemOpenClass);
                    }

                    // Accordion
                    if (link.closest(`.${navContainerClass}`).getAttribute('data-nav-type') == 'accordion') {
                        for (let sibling of link.parentNode.parentNode.children) {
                            if (sibling != link.parentNode && sibling.classList.contains(navItemOpenClass)) {
                                sibling.querySelectorAll(`:scope > .${navSubmenuClass}`).forEach(function(submenu) {
                                    new bootstrap.Collapse(submenu).hide();
                                    sibling.classList.remove(navItemOpenClass);
                                });
                            }
                        }
                    }
                });
            });
        });
    };

    // Resize main sidebar
    const sidebarMainResize = function() {

        // Elements
        const sidebarMainElement = document.querySelector('.sidebar-main'),
              sidebarMainToggler = document.querySelectorAll('.sidebar-main-resize'),
              resizeClass = 'sidebar-main-resized',
              unfoldClass = 'sidebar-main-unfold';


        // Config
        if (sidebarMainElement) {

            // Define variables
            const unfoldDelay = 150;
            let timerStart,
                timerFinish;

            // Toggle classes on click
            sidebarMainToggler.forEach(function(toggler) {
                toggler.addEventListener('click', function(e) {
                    // e.preventDefault();
                    sidebarMainElement.classList.toggle(resizeClass);
                    !sidebarMainElement.classList.contains(resizeClass) && sidebarMainElement.classList.remove(unfoldClass);
                    
                    let stateLeftPanel = "Y";
                    if(sidebarMainElement.classList.contains(resizeClass)) {
                        stateLeftPanel = "N";
                    }
                    BX.ajax({
                        url: "/include/ajax/left_panel_state.php",
                        method: 'POST',
                        data: {
                            "state": stateLeftPanel,
                        },
                    });
                    BX.onCustomEvent('ToggleMainLayout');

                    if (!$().floatingScroll) {
                        console.warn('Warning - jquery.floatingscroll.js is not loaded.');
                        return;
                    };
                    $(".fl-scrolls").floatingScroll("update");
                });                
            });

            // Add class on mouse enter
            sidebarMainElement.addEventListener('mouseenter', function() {
                clearTimeout(timerFinish);
                timerStart = setTimeout(function() {
                    sidebarMainElement.classList.contains(resizeClass) && sidebarMainElement.classList.add(unfoldClass);
                }, unfoldDelay);
            });

            // Remove class on mouse leave
            sidebarMainElement.addEventListener('mouseleave', function() {
                clearTimeout(timerStart);
                timerFinish = setTimeout(function() {
                    sidebarMainElement.classList.remove(unfoldClass);
                }, unfoldDelay);
            });
        }
    };

    // Toggle main sidebar
    const sidebarMainToggle = function() {

        // Elements
        const sidebarMainElement = document.querySelector('.sidebar-main'),
              sidebarMainRestElements = document.querySelectorAll('.sidebar:not(.sidebar-main):not(.sidebar-component)'),
              sidebarMainDesktopToggler = document.querySelectorAll('.sidebar-main-toggle'),
              sidebarMainMobileToggler = document.querySelectorAll('.sidebar-mobile-main-toggle'),
              sidebarCollapsedClass = 'sidebar-collapsed',
              sidebarMobileExpandedClass = 'sidebar-mobile-expanded';

        // On desktop
        sidebarMainDesktopToggler.forEach(function(toggler) {
            toggler.addEventListener('click', function(e) {
                e.preventDefault();
                sidebarMainElement.classList.toggle(sidebarCollapsedClass);
            });                
        });

        // On mobile
        sidebarMainMobileToggler.forEach(function(toggler) {
            toggler.addEventListener('click', function(e) {
                e.preventDefault();
                sidebarMainElement.classList.toggle(sidebarMobileExpandedClass);

                sidebarMainRestElements.forEach(function(sidebars) {
                    sidebars.classList.remove(sidebarMobileExpandedClass);
                });
            });                
        });
    };

    // Toggle secondary sidebar
    const sidebarSecondaryToggle = function() {

        // Elements
        const sidebarSecondaryElement = document.querySelector('.sidebar-secondary'),
              sidebarSecondaryRestElements = document.querySelectorAll('.sidebar:not(.sidebar-secondary):not(.sidebar-component)'),
              sidebarSecondaryDesktopToggler = document.querySelectorAll('.sidebar-secondary-toggle'),
              sidebarSecondaryMobileToggler = document.querySelectorAll('.sidebar-mobile-secondary-toggle'),
              sidebarCollapsedClass = 'sidebar-collapsed',
              sidebarMobileExpandedClass = 'sidebar-mobile-expanded';

        // On desktop
        sidebarSecondaryDesktopToggler.forEach(function(toggler) {
            toggler.addEventListener('click', function(e) {
                e.preventDefault();
                sidebarSecondaryElement.classList.toggle(sidebarCollapsedClass);
            });                
        });

        // On mobile
        sidebarSecondaryMobileToggler.forEach(function(toggler) {
            toggler.addEventListener('click', function(e) {
                e.preventDefault();
                sidebarSecondaryElement.classList.toggle(sidebarMobileExpandedClass);

                sidebarSecondaryRestElements.forEach(function(sidebars) {
                    sidebars.classList.remove(sidebarMobileExpandedClass);
                });
            });                
        });
    };

    // Toggle right sidebar
    const sidebarRightToggle = function() {

        // Elements
        const sidebarRightElement = document.querySelector('.sidebar-end'),
              sidebarRightRestElements = document.querySelectorAll('.sidebar:not(.sidebar-end):not(.sidebar-component)'),
              sidebarRightDesktopToggler = document.querySelectorAll('.sidebar-end-toggle'),
              sidebarRightMobileToggler = document.querySelectorAll('.sidebar-mobile-end-toggle'),
              sidebarCollapsedClass = 'sidebar-collapsed',
              sidebarMobileExpandedClass = 'sidebar-mobile-expanded';

        // On desktop
        sidebarRightDesktopToggler.forEach(function(toggler) {
            toggler.addEventListener('click', function(e) {
                e.preventDefault();
                sidebarRightElement.classList.toggle(sidebarCollapsedClass);
            });                
        });

        // On mobile
        sidebarRightMobileToggler.forEach(function(toggler) {
            toggler.addEventListener('click', function(e) {
                e.preventDefault();
                sidebarRightElement.classList.toggle(sidebarMobileExpandedClass);

                sidebarRightRestElements.forEach(function(sidebars) {
                    sidebars.classList.remove(sidebarMobileExpandedClass);
                });
            });                
        });
    };

    // Toggle component sidebar
    const sidebarComponentToggle = function() {

        // Elements
        const sidebarComponentElement = document.querySelector('.sidebar-component'),
              sidebarComponentMobileToggler = document.querySelectorAll('.sidebar-mobile-component-toggle'),
              sidebarMobileExpandedClass = 'sidebar-mobile-expanded';

        // Toggle classes
        sidebarComponentMobileToggler.forEach(function(toggler) {
            toggler.addEventListener('click', function(e) {
                e.preventDefault();
                sidebarComponentElement.classList.toggle(sidebarMobileExpandedClass);
            });                
        });
    };

    // Collapse card
    const cardActionCollapse = function() {

        // Elements
        const buttonClass = '[data-card-action=collapse]',
              cardCollapsedClass = 'card-collapsed';

        // Setup
        document.querySelectorAll(buttonClass).forEach(function(button) {
            button.onclick = function(e) {
                e.preventDefault();

                const parentContainer = button.closest('.card'),
                      collapsibleContainer = parentContainer.querySelectorAll(':scope > .collapse');

                if (parentContainer.classList.contains(cardCollapsedClass)) {
                    parentContainer.classList.remove(cardCollapsedClass);
                    collapsibleContainer.forEach(function(toggle) {
                        new bootstrap.Collapse(toggle, {
                            show: true
                        });
                    });
                }
                else {
                    collapsibleContainer.forEach(function(toggle) {
                        new bootstrap.Collapse(toggle, {
                            hide: true
                        });
                    });
                    parentContainer.classList.add(cardCollapsedClass);
                }
            }
        });
    };

    // Remove card
    const cardActionRemove = function() {

        // Elements
        const buttonClass = '[data-card-action=remove]',
              containerClass = 'card'

        // Config
        document.querySelectorAll(buttonClass).forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                button.closest(`.${containerClass}`).remove();
            });
        });
    };

    // Tooltip
    const componentTooltip = function() {
        const tooltipSelector = document.querySelectorAll('[data-bs-popup="tooltip"]');

        tooltipSelector.forEach(function(popup) {
            new bootstrap.Tooltip(popup, {
                boundary: '.page-content'
            });
        });
    };

    // Select2
    const componentSelect2 = function() {
        if (!$().select2) {
            console.warn('Warning - select2.min.js is not loaded.');
            return;
        };

        $('select.form-control, .form-select').select2({
            language: 'ru',
            minimumResultsForSearch: Infinity,
            dropdownAutoWidth: true
        });

        $('.form-control.select-image').select2({
            language: 'ru',
            templateResult: formatState,
        });
    };

    const formatState = function(state) {
        if (!state.id) { return state.text; }

        return $(
            `<span><img class="select-icon" src="${state.element.dataset.imgUrl}" alt="${state.text.trim()}" />${state.text.trim()}</span>`
        );
    };

    // Lightbox
    const componentLightbox = function() {
        if (typeof Lightbox === 'undefined') {
            console.warn('Warning - lightbox.min.js is not loaded.');
            return;
        }

        $('[data-bs-toggle="lightbox"]').click(Lightbox.initialize);
    };

    // Upload files
    const _addFile = function() {
        let counter = document.getElementById("files_counter").value;
        let tableWrapper = document.querySelector(".add_more_files");
        let input = tableWrapper.querySelector(`.input-file[name=FILE_${counter - 1}]`);
    
        if (input.value == false) {
            document.querySelector(`.input-file[name=FILE_${counter - 1}]`).click();
            return;
        }
    
        if (document.getElementById("files_counter").value < 5) {
            $(tableWrapper).append(
                '<div class="media-body" id="files_' + counter + '">' +
                    '<div class="upload-file">' +
                        '<img id="files_preview_' + counter + '">' +
                        '<input type="file" name="FILE_'+ counter +'" size="30" class="input-file" data-fouc onchange="App.showPreviewPicture('+counter+')">' +
                        '<span class="filename">' + BX.message('FILE_NOT_SELECTED_TEXT') + '</span>' +
                        '<span class="fileremove" onclick="App.removeFile(event)"><i class="ph-x ms-2 fs-sm"></i></span>'+
                    '</div>' +
                '</div>'
            );
    
            input = document.querySelector(".input-file[name=FILE_" + counter + "]");
    
            if (input) {
                _initFile(input);
            }
    
            input.click();
            document.getElementById("files_counter").value = ++counter;
        }
    };
    
    const _removeFile = function (event) {
        let wrapperFile;
        let counter = document.getElementById("files_counter").value;
    
        if (wrapperFile = event.target.closest('.media-body')) {
            wrapperFile.remove();
            document.getElementById("files_counter").value = --counter;
        }
    };

    const _initFile = function (input) {
        const label = input.nextElementSibling;
        input.addEventListener('change', viewFileName);

        function viewFileName(event) {
            let fileName = '';

            if (event.target) {
                fileName = event.target.value.split('\\').pop();
            }
    
            if (fileName) {
                label.innerHTML = fileName;
            }
        } 
    };    
    
    const _showPreviewPicture = function(key) {
        const selectedFile = event.target.files[0];
        const reader = new FileReader();

        if (!selectedFile) return;

        if (/^image/.test(selectedFile.type)) {
            reader.onload = function(){
                const output = document.getElementById('files_preview_' + key);
                output.src = reader.result;
            };
        }
        reader.readAsDataURL(selectedFile);
    }

    //
    // Return objects assigned to module
    //
    return {
        // Disable transitions before page is fully loaded
        initBeforeLoad: function() {
            detectOS();
            // transitionsDisabled();
        },

        // Enable transitions when page is fully loaded
        initAfterLoad: function() {
            transitionsEnabled();
        },

        // Initialize all components
        initComponents: function() {
            componentTooltip();
            componentSelect2();
            // componentPopover();
            // componentToTopButton();
        },

        initSelect2: function() {
            componentSelect2();
        },

        initLightbox: function() {
            componentLightbox();
        },

        // Initialize all navigations
        initNavigations: function() {
            navigationSidebar();
        },

        // Initialize all sidebars
        initSidebars: function() {
            sidebarMainResize();
            sidebarMainToggle();
            sidebarSecondaryToggle();
            sidebarRightToggle();
            sidebarComponentToggle();
        },

        // Initialize all card actions
        initCardActions: function() {
            // cardActionReload();
            cardActionCollapse();
            cardActionRemove();
        },

        // Initialize core
        initCore: function() {
            App.initBeforeLoad();
            App.initSidebars();
            App.initNavigations();
            App.initComponents();
            App.initCardActions();
            // App.initDropdowns();
        },

        // Upload file
        initFile: function(input) {
            _initFile(input);
        },

        addFile: function() {
            _addFile();
        },

        removeFile: function(event) {
            _removeFile(event);
        },
        
        showPreviewPicture: function(key) {
            _showPreviewPicture(key);
        }
    }
}();


// When content is loaded
document.addEventListener('DOMContentLoaded', function() {
    App.initCore();
});

// When page is fully loaded
window.addEventListener('load', function() {
    // App.initAfterLoad();
});