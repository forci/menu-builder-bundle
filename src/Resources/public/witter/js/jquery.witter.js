/*
 * Witter for jQuery
 *
 * TODO: Add a "restore" option with a default value of TRUE, which sets whether the notification can be restored via hover or tap on smartphones. Useful if you want the notification to fade anyway - implementation must involve not registering hover handlers AT ALL
 *
 * Copyright (c) 2014 Martin Kirilov
 * Dual licensed under the MIT and GPL licenses.
 *
 * Based on Gritter for jQuery - http://www.boedesign.com/
 *
 * Copyright (c) 2012 Jordan Boesch
 * Dual licensed under the MIT and GPL licenses.
 *
 * Date: December 06, 2014
 * Version: 1.0.1
 */
;(function ($) {

    /**
     * Set it up as an object under the jQuery namespace
     */
    $.witter = {};

    /**
     * Set up global options that the user can over-ride
     */
    $.witter.defaults = {
        title: '',
        position: 'top-right',
        class: '',
        theme: 'dark',
        fade: {
            in: {
                speed: 'medium',
                easing: ''
            },
            out: {
                speed: 1000,
                easing: ''
            }
        },
        close_selector: '.witter-close, .close',
        time: 6000,
        image: '',
        sticky: false,
        callbacks: {
            before_open: function () {
                //
            },
            after_open: function () {
                //
            },
            /**
             *
             * @param options - An object of options passed to the fade method
             * @param is_forced - Indicates whether it was closed by clicking on the close button
             */
            fade: function (options, is_forced) {
                //
            },
            before_close: function () {
                //
            },
            after_close: function () {
                //
            }
        },
        templates: {
            close: '<a class="witter-close" href="#" tabindex="1"><i class="fa fa-times"></i></a>',
            title: '<span class="witter-title">{title}</span>',
            item: '<div id="witter-item-{number}" class="witter-item-wrapper {theme}" style="display:none" role="alert"><div class="witter-top"></div><div class="witter-item">{close}{image}<div class="{class}">{title}<p>{text}</p></div><div style="clear:both"></div></div><div class="witter-bottom"></div></div>',
            html: '<div id="witter-item-{number}" class="witter-item-wrapper {theme} html" style="display:none" role="alert"><div class="witter-item">{html}<div style="clear:both"></div></div></div>',
            wrapper: '<div id="witter-wrappers"></div>'
        }
    };

    $.witter.stats = {
        instances: 0
    };

    $.witter.instances = {
        count: 0,
        instances: {},
        active: [],
        /**
         * Increments instances count, then uses it as the instance ID and returns it
         * @param instance
         * @returns {number}
         */
        add: function (instance) {
            var id = ++this.count;
            this.instances[id] = instance;
            this.setActive(id);
            return id;
        },
        get: function (instance) {
            if (typeof(instance) == 'number') {
                return this.instances[instance];
            }
            return instance;
        },
        setActive: function (id) {
            this.active.push(id);
        },
        setInactive: function (id) {
            this.active = $.grep(this.active, function (value) {
                return value != id;
            });
        },
        getActiveIds: function () {
            return this.active;
        }
    };

    $.witter.parse = function (template, data) {
        return template.replace(/\{([\w\.]*)\}/g, function (str, key) {
            var keys = key.split("."), v = data[keys.shift()];
            for (var i = 0, l = keys.length; i < l; i++) v = v[keys[i]];
            return (typeof v !== "undefined" && v !== null) ? v : "";
        });
    };

    /**
     * Add a witter notification to the screen
     */
    $.witter.add = function (params) {
        try {
            return new Witter(params || {});
        } catch (e) {
            var err = 'Witter Error: ' + e;
            (typeof(console) != 'undefined' && console.error) ? console.error(err, params) : alert(err);
        }
    };

    /**
     * Remove a witter notification from the screen
     */
    $.witter.remove = function (instance, options) {
        instance = $.witter.instances.get(instance);
        instance.fade(options || {});
    };

    /**
     * Remove a witter notification from the screen instantly
     */
    $.witter.removeNow = function (instance) {
        instance = $.witter.instances.get(instance);
        instance.removeElement();
    };

    /**
     * Remove all notifications
     */
    $.witter.removeAll = function (options) {
        var ids = $.witter.instances.getActiveIds();
        $.each(ids, function (index, instanceId) {
            var instance = $.witter.instances.get(instanceId);
            instance.fade(options || {});
        });
    };

    /**
     * Remove all notifications instantly
     */
    $.witter.removeAllNow = function () {
        var ids = $.witter.instances.getActiveIds();
        $.each(ids, function (index, instanceId) {
            var instance = $.witter.instances.get(instanceId);
            instance.removeElement();
        });
    };

    var wrappers = $('#witter-wrappers');
    if (wrappers.length == 0) {
        $('body').append($.witter.defaults.templates.wrapper);
        wrappers = $(wrappers.selector);
        $(['top-right', 'bottom-right', 'bottom-left', 'top-left', 'top', 'bottom']).each(function (index, className) {
            var div = $('<div/>').addClass('wrapper').addClass(className).appendTo(wrappers);
        });
    }

    var Witter = function (options) {
        if (typeof(options) == 'string') {
            options = {text: options};
        }

        if (options.text === null) {
            throw 'You must supply "text" parameter.';
        }

        /**
         * Set the notification to fade out after a certain amount of time
         */
        this.setFadeTimer = function () {
            var that = this;
            instance.fadeTimer = setTimeout(function () {
                that.fade(options, false);
            }, options.time);
        };

        /**
         * Fade out an element after it's been on the screen for x amount of time
         * @param params
         * @param is_forced
         */
        this.fade = function (params, is_forced) {
            var opts = $.extend(true, {}, options, params || {});

            instance.callbacks.fade.apply(instance, [opts, is_forced]);

            if (is_forced) {
                instance.element.off('mouseenter mouseleave');
            }

            if (opts.fade.out.speed) {
                instance.element.animate({
                    opacity: 0
                }, opts.fade.out.speed, function () {
                    $(this).slideUp(300, function() {
                        instance.removeElement();
                    });
                });
                return;
            }

            instance.removeElement();
        };

        this.removeElement = function () {
            instance.callbacks.before_close.apply(instance);
            $(instance.element).remove();
            $.witter.instances.setInactive(instance.id);
            instance.callbacks.after_close.apply(instance);
        };

        this.restoreItemIfFading = function () {
            clearTimeout(instance.fadeTimer);
            instance.element.stop().css({opacity: '', height: ''});
        };

        options = $.extend(true, {}, $.witter.defaults, options);

        var instance = this;

        var number = $.witter.instances.add(instance);

        this.id = number;

        this.callbacks = options.callbacks;

        var image_str = (options.image != '') ? '<img src="' + options.image + '" class="witter-image" />' : '',
            class_name = (options.image != '') ? 'witter-with-image' : 'witter-without-image';

        if (options.title) {
            options.title = $.witter.parse(options.templates.title, {title: options.title});
        }

        var template = options.html ? options.templates.html : options.templates.item;
        var itemTemplate = $.witter.parse(template, {
            title: options.title,
            text: options.text,
            html: options.html,
            position: options.position,
            close: options.templates.close,
            image: image_str,
            number: number,
            class: class_name,
            theme: options.theme
        });

        if (this.callbacks.before_open.apply(instance) === false) {
            // return prior to showing anything as before_open returned false - don't show a notification at all
            return this;
        }

        wrappers.find('.' + options.position).append(itemTemplate);

        var item = $('#witter-item-' + number);

        this.element = item;

        item.fadeIn({
            duration: options.fade.in.speed,
            easing: options.fade.in.easing,
            complete: function () {
                instance.callbacks.after_open.apply(instance);
            }
        });

        if (!options.sticky) {
            this.setFadeTimer();
        }

        $(item).on('mouseenter', function () {
            if (!options.sticky) {
                instance.restoreItemIfFading();
            }
            $(this).addClass('hover');
        }).on('mouseleave', function () {
            if (!options.sticky) {
                instance.setFadeTimer(instance, options);
            }
            $(this).removeClass('hover');
        });

        $(item).find(options.close_selector).click(function () {
            instance.fade(options, true);
            return false;
        });

        return this;
    };

})(jQuery);