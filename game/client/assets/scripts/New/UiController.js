var mvs = require('Mvs');
var GLB = require('GLBConfig');
cc.Class({
    extends: cc.Component,

    properties: {
        AN:{
            default:null,
            type:cc.Object,
        },//节点资源
        VS:{
            default:null,
            type:cc.Object,
        },//显示图像
        GM:{
            default:null,
            type:cc.Object,
        },//游戏管理
    },

    onLoad () {
        this.AN=cc.find("AssetsNode").getComponent("AssetsNode");
        this.VS=cc.find("ViewShow").getComponent("ViewShow");
        this.GM=cc.find("GameManager").getComponent("GameManager");
    },

    start () {
        // console.log(this.AN);
        this.AN.resetGame.node.on(cc.Node.EventType.TOUCH_START,() => {
            this.GM.LoadScene("NewGameScene");
        });
        this.AN.outHome.node.on(cc.Node.EventType.TOUCH_START,() => {
            if(window.Game.currentType === window.Game.type.OneVsOne){
                let result = mvs.engine.leaveRoom("I Love China");
                if (result !== 0)
                    return console.log('退出房间失败,错误码:' + result);
            }
            this.GM.LoadScene("Home");
        });

    },

});
