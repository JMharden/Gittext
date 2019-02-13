cc.Class({
    extends: cc.Component,

    properties: {
        AN:{
            default:null,
            type:cc.Object,
        },
        GD:{
            default:null,
            type:cc.Object,
        },
    },

    onLoad () {
        this.AN=cc.find("AssetsNode").getComponent("AssetsNode");
        this.GD=cc.find("GameData").getComponent("GameData");
    },

    update (dt) {
        this.SmoothMove(dt);
    },

    SmoothMove(dt){
        for(let i =0;i<this.AN.Player.length;i++){
            this.AN.Player[i].x=cc.misc.lerp(this.AN.Player[i].x,this.GD.playerTargetX[i],dt*8);
            this.AN.Player[i].y=cc.misc.lerp(this.AN.Player[i].y,this.GD.playerTargetY[i],dt*8);
        }
        // this.AN.Player[0].x=cc.misc.lerp(this.AN.Player[0].x,this.GD.playerTargetX[0],dt*8);
        // this.AN.Player[0].y=cc.misc.lerp(this.AN.Player[0].y,this.GD.playerTargetY[0],dt*8);
        // this.AN.Player[1].x=cc.misc.lerp(this.AN.Player[1].x,this.GD.playerTargetX[1],dt*8);
        // this.AN.Player[1].y=cc.misc.lerp(this.AN.Player[1].y,this.GD.playerTargetY[1],dt*8);
        this.AN.GameObj.x=cc.misc.lerp(this.AN.GameObj.x,this.GD.gameObjTargetX,dt*2);
        this.AN.GameObj.y=cc.misc.lerp(this.AN.GameObj.y,this.GD.gameObjTargetY,dt*2);
    },//平滑移动
});
