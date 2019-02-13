cc.Class({
    extends: cc.Component,

    properties: {
        GD:{
            default:null,
            type:cc.Object,
        },//游戏数据
        AN:{
            default:null,
            type:cc.Object,
        },//资源节点
        GM:{
            default:null,
            type:cc.Object,
        },//游戏管理


    },
    onLoad () {
        this.GD=cc.find("GameData").getComponent("GameData");
        this.AN=cc.find("AssetsNode").getComponent("AssetsNode");
        this.GM=cc.find("GameManager").getComponent("GameManager");

    },
    start(){
        this.AN.lBtn.node.on(cc.Node.EventType.TOUCH_START,  (event) => {
            if(this.GD.isDie===false){
                this.GM.Click("L");

            }

        });
        this.AN.rBtn.node.on(cc.Node.EventType.TOUCH_START,  (event) => {
            if (this.GD.isDie===false){
                this.GM.Click("R");
            }
        });
    }

});
