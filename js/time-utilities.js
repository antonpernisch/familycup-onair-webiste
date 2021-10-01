function TimeUtilities() {
    this.RawTimeRegex = "^[0-9]+:[0-9]+\.[0-9]+$";
    return;
}

TimeUtilities.prototype = {
    GetDifference: function (defaultTime, time) {
        if (defaultTime.search(this.RawTimeRegex) != -1 && time.search(this.RawTimeRegex) != -1) {
            let splittedDefaultTime = defaultTime.split(".");
            let splittedTime = time.split(".");
            switch (splittedDefaultTime[1].length) {
                case 1:
                    splittedDefaultTime[1] += "00";
                    break;
                case 2:
                    splittedDefaultTime[1] += "0";
                    break;
                default:
                    break;
            }
            switch (splittedTime[1].length) {
                case 1:
                    splittedTime[1] += "00";
                    break;
                case 2:
                    splittedTime[1] += "0";
                    break;
                default:
                    break;
            }
            defaultTime = Date.parseString(splittedDefaultTime[0], "m:s").setMilliseconds(splittedDefaultTime[1]);
            time = Date.parseString(splittedTime[0], "m:s").setMilliseconds(splittedTime[1]);
            return time - defaultTime;
        } else {
            return false;
        }
    },

    ConvertToReadable: function (raw) {
        if (raw === false) {
            return `<span class='text-danger'><i class="far fa-times-circle mr-2"></i>Chyba</span>`;
        }
        let dateObj = new Date(raw);
        mins = dateObj.getUTCMinutes();
        secs = dateObj.getUTCSeconds();
        millis = dateObj.getUTCMilliseconds();
        return `${mins}:${secs}.${millis}`;
    },

    ConvertToReadableHM: function (raw) {
        let hours = Math.floor(raw / 60).toString();
        let minutes = raw - hours * 60;
        hours = ('0' + hours).slice(-2);
        minutes = ('0' + minutes).slice(-2);
        return `${hours}:${minutes}`;
    },

    Debug: function (time) {
        if (time.search(this.RawTimeRegex) != -1) {
            let splitted = time.split(".");
            return Date.parseString(splitted[0], "m:s").setMilliseconds(splitted[1]);
        } else {
            return false;
        }
    }
};