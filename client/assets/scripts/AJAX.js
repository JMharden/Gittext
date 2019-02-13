let GLB = require("GLBConfig");
cc.Class({
    extends: cc.Component,

    properties: {
        AJAX:{
            default:null,
            type: cc.Button,
        },

    },

    // LIFE-CYCLE CALLBACKS:

    onLoad () {},

    start () {
        this.AJAX.node.on(cc.Node.EventType.TOUCH_END, () => {
            GLB._GLBFun.ajax({
                url: '/index.php?m=Index&c=index&a=startGame',
                type: 'post',
                data: {
                    type: 1,
                    status: 1,
                    uid: 76,
                    start_time: '1548317846569',
                    mark: 1234,
                },
                success: function (res) {
                    console.log(res);
                },
                error: function (err) {
                    console.log(err);
                }
            })

        })
    },

    update (dt) {},
});
