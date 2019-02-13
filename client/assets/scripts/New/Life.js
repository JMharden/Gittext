cc.Class({
    extends: cc.Component,

    properties: {
        GD:{
            default:null,
            type:cc.Object,
        },//游戏数据
        itNode:{
            default:null,
            type:cc.Node,
        },
        meIndex:0,


    },
    onLoad () {
        this.GD = cc.find("GameData").getComponent("GameData");
        this.meIndex+=this.GD.cubeIndex;
    },


    update (dt) {
        if(this.GD.life>=this.meIndex){
            this.itNode.destroy();
        }

    },
});
