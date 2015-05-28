/**
 * Created by Administrator on 2015/1/7.
 */
var EventUtil = {
    addHandler: function (element, type, handler) {
        if (element) {
            if (element.addEventListener) {
                element.addEventListener(type, handler, false);
            } else if (element.attachEvent) {
                element.attachEvent('on' + type, handler);
            } else {
                element["on" + type] = handler;
            }
        }
    },
    getEvent: function (event) {
        return event ? event : window.event;
    },
    getTarget: function (event) {
        return event.target || event.srcElement;
    },
    preventDefault: function (event) {
        if (event.preventDefault) {
            event.preventDefault();
        } else {
            event.returnValue = false;
        }
    },
    stopPropagation: function (event) {
        if (event.stopPropagation) {
            event.stopPropagation();
        } else {
            event.cancelBubble = true;
        }
    },
    removeHandler: function (element, type, handler) {
        if (element.removeEventListener) {
            element.removeEventListener(type, handler, false);
        } else if (element.detachEvent) {
            element.detachEvent("on" + type, handler);
        } else {
            element["on" + type] = null;
        }
    }
};
var startPos, endPos, posLeft, newPosLeft;
function handleTouchEvent(event) {
    if (event.touches.length == 1) {
        var output = document.getElementById("product-nav");
        posLeft = output.offsetLeft;
        switch (event.type) {
            case "touchstart":
                startPos = {x: event.touches[0].pageX, y: event.touches[0].pageY};
//                alert(startPos.x);
                break;
            case "touchmove":
                event.preventDefault(); //阻止滚动
                endPos = {x: event.touches[0].pageX - startPos.x, y: event.touches[0].pageY - startPos.y};
//                alert(posLeft);
//                alert(endPos.x);

                if (endPos.x > 10) {//向右滑动
//                        alert(posLeft);
                    newPosLeft = parseInt(endPos.x) + parseInt(posLeft);
                    if (posLeft == 0 || posLeft <= 15) {
                        if (newPosLeft > 0) {
                            output.style.left = '0px';
                        } else {
                            output.style.left = (newPosLeft) + 'px';
                        }

                    } else {

                        output.style.left = newPosLeft + 'px';
                        return false;
                    }
                } else if (endPos.x < -10) {//向左滑动
                    newPosLeft = parseInt(endPos.x) + parseInt(posLeft);
                    if (newPosLeft < -320) {
                        return false;
                    } else {
//                        alert(newPosLeft);
                        output.style.left = newPosLeft + 'px';
                        return false;
                    }
                }
                break;
//            case "touchend":
//                output.style.left = newPosLeft+'px';
//                break;


        }
    }
}
var output_container = document.getElementById("product-nav-container");
EventUtil.addHandler(output_container, "touchstart", handleTouchEvent);
EventUtil.addHandler(output_container, "touchend", handleTouchEvent);
EventUtil.addHandler(output_container, "touchmove", handleTouchEvent);

