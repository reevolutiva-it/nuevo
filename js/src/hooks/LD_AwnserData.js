class LD_AwserData{
    constructor(){
        this._answerData = [];
    }

    add(objeto){
        this._answerData.push(objeto);
    }

}

class LD_AwserData_item{
    constructor(points, correct, answer){    
        this._points = points;
        this._gradedType = "text";
        this._gradingProgression = "not-graded-none";
        this._correct = correct;
        this._html = false;
        this._graded = 1,
        this._sortString = ",";
        this._type = "answer";
        this._sortStringHtml = false;
        this._answer = answer;
        this._answerType = "single";
        
    }
}

export  {LD_AwserData, LD_AwserData_item};