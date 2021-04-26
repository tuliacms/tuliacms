$(function () {
    let body = $('body');
    let sc = new Scroller(10, function () {
        body.removeClass('body-scrolled');
    }, function () {
        body.addClass('body-scrolled');
    });

    sc.init();
});

let Scroller = function (breakpoint, before, after) {
    this.breakpoint = breakpoint;
    this.before = before;
    this.after = after;
    this.isBefore = false;

    this.init = function () {
        let self = this;

        $(window).scroll(function () {
            self.decide();
        });

        self.decide();
    };

    this.decide = function () {
        if(this.isBefore) {
            if ($(window).scrollTop() < this.breakpoint) {
                this.isBefore = false;
                this.before();
            }
        } else {
            if ($(window).scrollTop() > this.breakpoint) {
                this.isBefore = true;
                this.after();
            }
        }
    };
};
