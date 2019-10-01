/* https://github.com/tylerecouture/summernote-lists  */

(function(factory) {
  /* global define */
  if (typeof define === "function" && define.amd) {
    // AMD. Register as an anonymous module.
    define(["jquery"], factory);
  } else if (typeof module === "object" && module.exports) {
    // Node/CommonJS
    module.exports = factory(require("jquery"));
  } else {
    // Browser globals
    factory(window.jQuery);
  }
})(function($) {
    $.extend(true, $.summernote.lang, {
        "en-US": {
            numListStyleTypes: {
                tooltip: "List Styles",
                labelsListStyleTypes: [
                    "Numbered",
                    "Lower Alpha",
                    "Upper Alpha",
                    "Lower Roman",
                    "Upper Roman"
                ]
            },
            bullListStyleTypes: {
                tooltip: "List Styles",
                labelsListStyleTypes: [
                    "Disc",
                    "Circle",
                    "Square"
                ]
            }
        }
    });

    $.extend($.summernote.options, {
        numListStyleTypes: {
        /* Must keep the same order as in lang.imageAttributes.tooltipShapeOptions */
            styles: [
                "decimal",
                "lower-alpha",
                "upper-alpha",
                "lower-roman",
                "upper-roman"
            ]
        },
        bullListStyleTypes: {
            /* Must keep the same order as in lang.imageAttributes.tooltipShapeOptions */
            styles: [
                "disc",
                "circle",
                "square"
            ]
        }
    });

    $.extend($.summernote.plugins, {
        numListStyles: function(context) {
            var self = this;
            var ui = $.summernote.ui;
            var options = context.options;

            var orderDecimal = ui.button({
                contents: "<img src='" + BASE_URL + "/img/list_123.png' width=25/>",
                click: function() { self.updateStyleType('decimal'); }
            });
            var orderAlpha = ui.button({
                contents: "<img src='" + BASE_URL + "/img/list_abc.png' width=25/>",
                click: function() { self.updateStyleType(LOCALE == 'he' ? 'hebrew' : 'upper-alpha'); }
            });
            var orderMultilevel = ui.button({
                contents: "<img src='" + BASE_URL + "/img/list_multi_level.png' width=25/>",
                click: function() { self.updateStyleType('multi-level'); }
            });
        
            context.memo("button.numListStyles", function() {
                return ui.buttonGroup([
                    ui.button({
                        className: 'btn-caret',
                        contents: ui.icon(options.icons.caret),
                        data: { toggle: 'dropdown' }
                    }),
                    ui.dropdown([
                        ui.buttonGroup({
                            className: 'ordered-list',
                            children: [orderDecimal, orderAlpha, orderMultilevel]
                        })
                    ])
                ]).render();
            })

            self.updateStyleType = function(style) {
                context.invoke("beforeCommand");
                if(style == 'multi-level') {
                    self.getParentList().css('list-style-type', 'none');
                    self.getParentList().addClass('multi_level');
                    self.getParentList().find('ol').addClass('multi_level');
                    $.each(self.getParentList().find('ol'), function(index, ol) {
                        $(ol).prev('li').append($(ol));
                    });
                } else {
                    self.getParentList().removeClass('multi_level');
                    self.getParentList().find('ol').removeClass('multi_level');
                    self.getParentList().css("list-style-type", style);
                }
                context.invoke("afterCommand");
            }

            self.getParentList = function () {
                if (window.getSelection) {
                    var $focusNode = $(window.getSelection().focusNode);
                    var $parentList = $focusNode.closest("div.note-editable ol, div.note-editable ul");
                    return $parentList;
                }
                return null;
            }
        }
    });

    // Extends plugins for emoji plugin.
    $.extend($.summernote.plugins, {
        bullListStyles: function(context) {
            var self = this;
            var ui = $.summernote.ui;
            var options = context.options;

            var orderDisc = ui.button({
                contents: "<img src='" + BASE_URL + "/img/list_disc.png' width=25/>",
                click: function() { self.updateStyleType('disc'); }
            });
            var orderCircle = ui.button({
                contents: "<img src='" + BASE_URL + "/img/list_circle.png' width=25/>",
                click: function() { self.updateStyleType('circle'); }
            });
            var orderSquare = ui.button({
                contents: "<img src='" + BASE_URL + "/img/list_square.png' width=25/>",
                click: function() { self.updateStyleType('square'); }
            });
            var orderCheck = ui.button({
                contents: "<img src='" + BASE_URL + "/img/list_check.png' width=25/>",
                click: function() { self.updateStyleType('check'); }
            });
            var orderDelete = ui.button({
                contents: "<img src='" + BASE_URL + "/img/list_delete.png' width=25/>",
                click: function() { self.updateStyleType('delete'); }
            });
        
            context.memo("button.bullListStyles", function() {
                return ui.buttonGroup([
                    ui.button({
                        className: 'btn-caret',
                        contents: ui.icon(options.icons.caret),
                        data: { toggle: 'dropdown' }
                    }),
                    ui.dropdown([
                        ui.buttonGroup({
                            className: 'unordered-list',
                            children: [orderDisc, orderCircle, orderSquare, orderCheck, orderDelete]
                        })
                    ])
                ]).render();
            })

            self.updateStyleType = function(style) {
                context.invoke("beforeCommand");
                if(style == 'check') {
                    self.getParentList().removeClass('delete');
                    self.getParentList().addClass('check');
                } else if(style == 'delete') { 
                    self.getParentList().removeClass('check');
                    self.getParentList().addClass('delete');
                } else {
                    self.getParentList().removeClass('check');
                    self.getParentList().removeClass('delete');
                    self.getParentList().css("list-style-type", style);
                }
                context.invoke("afterCommand");
            }

            self.getParentList = function () {
                if (window.getSelection) {
                    var $focusNode = $(window.getSelection().focusNode);
                    var $parentList = $focusNode.closest("div.note-editable ol, div.note-editable ul");
                    return $parentList;
                }
                return null;
            }
        }
    });
});
