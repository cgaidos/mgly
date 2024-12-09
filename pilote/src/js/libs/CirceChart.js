// Paul Irish polyfill
window.requestAnimFrame = (function () {
    return window.requestAnimationFrame ||
        window.webkitRequestAnimationFrame ||
        window.mozRequestAnimationFrame ||
        function (callback) {
            window.setTimeout(callback, 1000 / 60);
        }
})();

module.exports = (function ($) {
    /**
     * @param selector
     * @constructor
     */
    function CircleChart(selector) {
        this.selector = $(selector)[0];

        this.background = $(selector).css('background-color');
        $(selector).css('background-color', 'transparent');

        var canvas = this.selector;
        this.ctx = canvas.getContext('2d');
        this.canvasHeight = canvas.height;
        this.canvasWidth = canvas.width;
        this.canvasCenterVert = canvas.height / 2;
        this.canvasCenterHoriz = canvas.width / 2;
        this.speed = 10;
        this.startAngle = this._degreesToRadians(-90);
        this.endAngle = this._percentToRadius($(selector).data('percent')) + this.startAngle;
        this.progress = this.startAngle;
    }

    CircleChart.prototype._draw = function () {
        this.ctx.clearRect(0, 0, this.canvasWidth, this.canvasHeight);

        if (this.progress < this.endAngle) {
            this.progress += this._degreesToRadians(5);
        }

        if (this.progress > this.endAngle) {
            this.progress = this.endAngle;
        }

        var angle360 = this._degreesToRadians(360);

        this._pie(this.canvasCenterHoriz, this.canvasCenterVert, 100, 0, angle360, false, '#e6e6e6');
        this._pie(this.canvasCenterHoriz, this.canvasCenterVert, 100, this.startAngle, this.progress, false, this.background);
    };

    CircleChart.prototype._degreesToRadians = function (degrees) {
        return ( degrees * Math.PI ) / 180;
    };

    CircleChart.prototype._percentToRadius = function (percent) {
        return this._degreesToRadians(360 * percent / 100);
    };

    CircleChart.prototype._pie = function (x, y, radius, startAngle, endAngle, direction, fillStyle) {
        this.ctx.beginPath();
        this.ctx.fillStyle = fillStyle;
        this.ctx.moveTo(this.canvasCenterHoriz, this.canvasCenterVert);
        this.ctx.arc(x, y, radius, startAngle, endAngle, direction);
        // this.ctx.closePath();
        // this.ctx.stroke();
        this.ctx.fill();
    };

    CircleChart.prototype.create = function () {
        var fps = 60;
        var self = this;
        (function animate() {
            self._draw();
            requestAnimFrame(animate);
        }.bind(this))();
    };

    return CircleChart;
})(jQuery);

