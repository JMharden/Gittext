const log4js = require('log4js');
const textEncoding = require('text-encoding');
var glb = require("./global");

const log = log4js.getLogger();

class Room {
    /**
     * Creates an instance of Room.
     * @param {number} gameID 
     * @param {number} roomID 
     * @param {Object} pushHander 
     * @memberof Room
     */
    constructor(gameID, roomID, pushHander) {
        this.gameID = gameID;
        this.roomID = roomID;
        this.pushHander = pushHander;
        this.players = new Map();
        this.scene = {};
        this.star = {x: 0, y: 0};
        this.isStarted = false;
        this.isFork = 0;
    }

    /**
     * 玩家进入房间
     * @param {number} userID 
     * @memberof Room
     */
    playerEnter(userID) {
        this.players.set(userID, {
            position: {x: 0, y: 0},
            score: 0,
            ready: false,
        });
    }

    /**
     * 玩家退出房间
     * @param {number} userID 
     * @memberof Room
     */
    playerExit(userID) {
        this.players.delete(userID);
    }

    /**
     * 房间事件
     * @param {number} userID 
     * @param {string} event 
     * @memberof Room
     */
    roomEvent(userID, event) {
        if (userID && event) {
            let action = event.action;
            let player;
            switch (action) {
                case glb.MAP_INIT:
                    let MapEventInit = {
                        action: glb.MAP_INIT,
                        user: userID,
                        mapPack: this.returnRandom(1,1000),
                        round: event.round,
                    };
                    this.sendEvent(MapEventInit);
                    // this.sendMapEvent(MapEventInit,userID);
                    // log.info('receive scene info:', MapEventInit);
                    // 初始化星星
                    // this.spawnNewStar();
                    break;
                case glb.USER_ALIKE:
                    log.info("同步用户信息");
                    break;
                case glb.PLAYER_POSITION_EVENT:
                    player = this.players.get(userID);
                    if (player) {
                        player.position.x = event.x;
                        player.position.y = event.y;
                        this.checkStar(userID, player);
                    }
                    break;
                case glb.GAME_READY:
                    player = this.players.get(userID);
                    if (player) {
                        player.ready = true;
                        this.checkGameStart();
                    }
                    // glb.cubeX = 350;//初始化地图
                    // glb.RAND_MAP_ARR_S = [];
                    // glb.RAND_MAP_ARR = [];
                    break;
                default:
                    log.warn('unknown action:', action);
                    break;
            }
        }
    }

    /**
     * 创建地图包*/
    spawnNewMap(val,round) {
        if(glb.RAND_MAP_ARR_S.length===round){
            log.info("开始生成地图");
            for(let i=0;i<val;i++){
                if (glb.MAP_LENGTH <= 0) {//随机方向与同时生成的方框
                    glb.MAP_DIRECTION = this.returnNum(-1, 1);
                    glb.MAP_LENGTH = this.returnRandom(1, 5);
                }
                glb.RAND_MAP_ARR[i] = glb.cubeX += (glb.MAP_DIRECTION * 100);
                glb.MAP_LENGTH--;
            }
            log.info("地图包资源是：",glb.RAND_MAP_ARR);
            glb.RAND_MAP_ARR_S.push(glb.RAND_MAP_ARR);
            log.info("结束:",glb.cubeX);
            log.info("地图包总资源是s：",glb.RAND_MAP_ARR_S[round]);
        }

    }

    returnNum(min,max){
        if (Math.random() < 0.5) {
            return min;
        } else {
            return max;
        }
    }//返回二个数的其中之一

    returnRandom(min,max){
        let choices = max - min + 1;
        return Math.floor(Math.random() * choices + min);
    }//返回两个数之间的随机整数

    /**
     * 检查房间内是否所有人都已经准备
     * @memberof Room
     */
    checkGameStart() {
        if (!this.isStarted && this.players.size >= glb.MAX_PLAYER_COUNT) {
            let allReady = true;
            for (let [k, p] of this.players) {
                if (!p.ready) {
                    allReady = false;
                }
            }
            if (allReady) {
                // 房间停止加人
                this.pushHander.joinOver({
                    gameID: this.gameID, 
                    roomID: this.roomID,
                });
                // 通知房间内玩家开始游戏
                this.notifyGameStart();
                this.isStarted = true;
            }
        }
    }

    /**
     * 通知客户端开始游戏
     * @memberof Room
     */
    notifyGameStart() {
        let userIds = [];
        for (let id of this.players.keys()) {
            userIds.push(id);
        }
        let event = {
            action: glb.GAME_START_EVENT,
            userIds: userIds,
        };
        this.sendEvent(event);
        // log.info('notifyGameStart event:', event);
    }

     /**
      * 随机返回“新星星”的位置
      * @memberof Room
      */
     getNewStarPosition() {
        this.star.x = randomMinus1To1() * this.scene.starMaxX;
        this.star.y = this.scene.groundY + random0To1() * this.scene.playerJumpHeight + this.scene.compensation;
    }

    /**
     * 生成“新星星”
     * @memberof Room
     */
    spawnNewStar() {
        this.getNewStarPosition();
        let event = {
            action: glb.NEW_START_EVENT,
            x: this.star.x,
            y: this.star.y,
        }
        this.sendEvent(event);
        // log.info('spawnNewStar event:', event);
    }

    /**
     * 计算玩家和星星之间距离的平方
     * @param {Object} position 
     * @param {number} position.x
     * @param {number} position.y
     * @returns 
     * @memberof Room
     */
    getDistanceSQ(position) {
        let v1 = this.star;
        let v2 = position;
        let v = {
            x: v1.x - v2.x,
            y: v1.y - v2.y,
        }
        return v.x * v.x + v.y * v.y;
    }

    /**
     * 判断星星是否被收集
     * @param {number} userID 
     * @param {Object} player 
     * @param {number} player.score
     * @param {Object} player.position
     * @param {number} player.position.x
     * @param {number} player.position.y
     * @memberof Room
     */
    checkStar(userID, player) {
        if (this.getDistanceSQ(player.position) < this.scene.pickRadius * this.scene.pickRadius) {
            player.score += 1;
            let event = {
                action: glb.GAIN_SCORE_EVENT,
                userId: userID,
                score: player.score,
            }
            // log.info('send gain score event:', event);
            this.sendEvent(event);
            
            // 产生新星星 
            this.spawnNewStar();
        }
    }

    sendMapEvent(event,userID){
        log.info(userID);
        let content = new textEncoding.TextEncoder("utf-8").encode(JSON.stringify(event));
        this.pushHander.pushEvent({
            gameID: this.gameID,
            roomID: this.roomID,
            pushType: 1,
            destsList: [userID],
            content: content,
        });
    }

    /**
     *  推送房间消息
     * @param {Object} event
     * @memberof Room
     */
    sendEvent(event) {
        let content = new textEncoding.TextEncoder("utf-8").encode(JSON.stringify(event));
        this.pushHander.pushEvent({
            gameID: this.gameID, 
            roomID: this.roomID, 
            pushType: 3,
            content: content,
        });
    }
}

/**
 * returns a random float between -1 and 1
 * @return {Number}
 */
function randomMinus1To1() {
    return (Math.random() - 0.5) * 2;
}

/**
 * returns a random float between 0 and 1, use Math.random directly
 * @return {Number}
 */
function random0To1() {
    return Math.random();
}

module.exports = Room;