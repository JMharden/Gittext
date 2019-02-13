cc.Class({
    extends: cc.Component,

    properties: {
        AN:{
            default:null,
            type:cc.Object,
        },
        GD:{
            default: null,
            type: cc.Object,
        }
    },

    onLoad () {
        this.AN=cc.find("AssetsNode").getComponent("AssetsNode");
        this.GD=cc.find("GameData").getComponent("GameData");
    },

    start () {

    },
    UiState(val){
        if(val === 0){

        }else if(val===1){

        }else if(val===2){
            this.GameOverShow();
            console.log("GameOver");
        }else{
            console.log("GameState null");
        }

    },

    GameOverShow(){
        this.AN.GameOver.active=true;
        this.AN.scoreLabel.string = this.GD.score;
    },//GameOver显示画面

    // update (dt) {},
});
