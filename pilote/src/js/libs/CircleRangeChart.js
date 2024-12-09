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
    function CircleRangeChart(selector) {
        this.selector = $(selector)[0];

        this.color = $(selector).css('color');

        var canvas = this.selector;
        this.ctx = canvas.getContext('2d');
        this.radius = 113;
        this.radiusPoint = 8;

        this.canvasHeight = canvas.height;
        this.canvasWidth = canvas.width;
        this.canvasCenterVert = canvas.height / 2;
        this.canvasCenterHoriz = canvas.width / 2;

        this.rangeStart= this._degreesToRadians(-225);
        this.rangeEnd  = this._degreesToRadians(45);

        this.endAngle = this._currentToRadius($(selector).data('current'), $(selector).data('max')) + this.rangeStart;
        this.progress = this.rangeStart;
    }

    CircleRangeChart.prototype._draw = function () {
        this.ctx.clearRect(0, 0, this.canvasWidth, this.canvasHeight);

        // end of slider
        if (this.progress < this.endAngle) {
            this.progress += this._degreesToRadians(5);
        }

        if (this.progress > this.endAngle) {
            this.progress = this.endAngle;
        }

        var coordinates = this._angleCoordinates(this.canvasCenterHoriz, this.canvasCenterVert, this.radius, this.progress);
        this.pointCenterVert = coordinates[1];
        this.pointCenterHoriz = coordinates[0];

        // background
        this._pie(this.canvasCenterHoriz, this.canvasCenterVert, this.radius, this.rangeStart, this.rangeEnd, false, '#e6e6e6');

        // chart
        this._pie(this.canvasCenterHoriz, this.canvasCenterVert, this.radius, this.rangeStart, this.progress, false, this.color);

        // point
        this._pieFill(this.pointCenterHoriz, this.pointCenterVert, this.radiusPoint, 0, this._degreesToRadians(360) , false, this.color);
    };

    CircleRangeChart.prototype._degreesToRadians = function (degrees) {
        return ( degrees * Math.PI ) / 180;
    };

    CircleRangeChart.prototype._currentToRadius = function (current, max) {
        return this._degreesToRadians(270 * current / max);
    };

    CircleRangeChart.prototype._angleCoordinates = function getPoint(c1, c2, radius, angle){
        return [
            c1 + Math.cos(angle) * radius,
            c2 + Math.sin(angle) * radius
        ];
    };

    CircleRangeChart.prototype._pie = function (x, y, radius, startAngle, endAngle, direction, fillStyle) {
        this.ctx.beginPath();
        this.ctx.lineWidth = 3;
        this.ctx.strokeStyle = fillStyle;
        this.ctx.arc(x, y, radius, startAngle, endAngle, direction);
        this.ctx.stroke();
    };

    CircleRangeChart.prototype._pieFill = function (x, y, radius, startAngle, endAngle, direction, fillStyle) {
        this.ctx.beginPath();
        this.ctx.fillStyle = fillStyle;
        this.ctx.arc(x, y, radius, startAngle, endAngle, direction);
        this.ctx.fill();
    };

    CircleRangeChart.prototype.create = function () {
        var fps = 60;
        var self = this;
        (function animate() {
            self._draw();
            requestAnimFrame(animate);
        }.bind(this))();
    };

    return CircleRangeChart;
})(jQuery);

