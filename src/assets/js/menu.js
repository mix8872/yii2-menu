document.menu = function (config) {
    switch (false) {
        case config.updateUrl:
            throw "updateUrl attribute is empty or undefined!";
    }

    var containerSelector = config.containerSelector || '#menu_items-container';
    var addMenuBtnSelector = config.addMenuBtnSelector || '.js-add-menu-item';

    this.updateUrl = config.updateUrl;
    this.form = $('form');
    this.container = $(containerSelector);
    this.itemsSelector = config.itemsSelector || 'li';
    this.itemContainerSelector = config.itemContainerSelector || '.js-item';
    this.rootId = this.container.data('id');
    this.itemDetailsSelector = config.itemDetailsSelector || '.js-details';
    this.moreBtnSelector = config.moreBtnSelector || '.js-more';
    this.deleteBtnSelector = config.deleteBtnSelector || '.js-delete';
    this.itemTitleSelector = config.itemTitleSelector || '.js-name-link';
    this.modalSelector = config.modalSelector || '#menu-item-modal';
    this.modal = $(this.modalSelector);
    this.addMenuBtn = $(addMenuBtnSelector);
    this.deleteConfirmMsg = config.deleteConfirmMsg || 'Удалить пункт меню и его дочерние пункты?';

    this.initSortable = this.initSortableContainer.bind(this);
    this.init();
};

document.menu.prototype = {
    init: function () {
        this.initSortable();
        $(document)
            .on('show.bs.collapse', this.itemDetailsSelector, this.toggleCollapseBtn.bind(this))
            .on('hide.bs.collapse', this.itemDetailsSelector, this.toggleCollapseBtn.bind(this))
            .on('mouseover', this.deleteBtnSelector, this.toggleItemBg.bind(this))
            .on('mouseout', this.deleteBtnSelector, this.toggleItemBg.bind(this))
            .on('keyup', this.itemDetailsSelector + ' input.name', this.fillName.bind(this))
            .on('keyup', this.itemDetailsSelector + ' input.url', this.fillUrl.bind(this))
            .on('click', this.deleteBtnSelector, this.deleteItem.bind(this));
        this.addMenuBtn.on('click', this.addItem.bind(this));
        this.form.on('afterValidate', this.formExpandOnError.bind(this));
    },
    initSortableContainer: function () {
        this.container.nestedSortable({
            forcePlaceholderSize: true,
            handle: 'div',
            helper: 'clone',
            items: this.itemsSelector,
            opacity: '.6',
            placeholder: 'placeholder',
            revert: 250,
            tabSize: 25,
            tolerance: 'pointer',
            isTree: true,
            expandOnHover: 700,
            startCollapsed: false,
            stop: this.sortStop.bind(this)
        });
    },
    sortStop: function (e, obj) {
        var id = parseInt($(obj.item).data('id'));
        var parentLi = $(obj.item).parents('li'); //родительский li
        var prev_li = $(obj.item).prev('li'); //текущий предыдущий li
        var next_li = $(obj.item).next('li'); //текущий следующий li

        var data = {
            parent_id: typeof parentLi[0] == 'undefined' ? this.rootId : $(parentLi[0]).data('id'),
            prev_id: typeof prev_li[0] == 'undefined' ? 0 : prev_li.data('id'),
            next_id: typeof next_li[0] == 'undefined' ? 0 : next_li.data('id')
        };

        var request = this.sendRequest.bind(this);
        request(this.updateUrl + '/?id=' + id, data);

        $.toast({
            text: "Порядок сохранен",
            position: "top-center",
            icon: "success",
            hideAfter: 3000,
            stack: 15
        });

    },
    sendRequest: function (url, data, callback) {
        $.ajax({
            url: url,
            type: 'post',
            data: data,
            success: callback || null
        });
    },
    toggleCollapseBtn: function (e) {
        $(e.currentTarget).closest(this.itemContainerSelector).find(this.moreBtnSelector).toggleClass('fa-chevron-down fa-chevron-up');
    },
    toggleItemBg: function (e) {
        $(e.currentTarget).closest(this.itemsSelector).find(this.itemContainerSelector).toggleClass('bg-danger');
    },
    fillName: function (e) {
        var current = $(e.currentTarget);
        current.closest(this.itemContainerSelector).find(this.itemTitleSelector).text(current.val());
    },
    fillUrl: function (e) {
        var current = $(e.currentTarget);
        current.closest(this.itemContainerSelector).find(this.itemTitleSelector).attr('href', current.val());
    },
    addItem: function (e) {
        e.preventDefault();
        var that = $(e.currentTarget);
        this.sendRequest(that.attr('href'), {}, this.addSuccess.bind(this));
    },
    addSuccess: function (r) {
        if (r.success) {
            this.container.append($(r.item));
            this.initSortable();
            this.modal.modal('hide');
        }
    },
    deleteItem: function (e) {
        e.preventDefault();
        if (!confirm(this.deleteConfirmMsg)) {
            return false;
        }
        var that = $(e.currentTarget);
        var item = that.closest(this.itemsSelector);
        var id = that.data('id');
        this.sendRequest(that.attr('href'), id[1], this.deleteSuccess.bind(item));
    },
    deleteSuccess: function (r) {
        if (r.success) {
            this.animate({
                opacity: 0
            }, 300).remove();
        }
    },
    formExpandOnError: function (e, messages) {
        var $this = this;
        var wrondFields = $(".has-error");
        if(wrondFields.length) {
            wrondFields.each(function (i, item) {
                var details = $(item).closest($this.itemDetailsSelector);
                if (!details.hasClass('show')) {
                    details.collapse('show');
                }
            });
        }
    }
};


