Tulia.Filemanager.Cache = function () {
    this.storage = localStorage;
    this.keyPrefix = 'tuliacms.filemanager.';

    this.get = function (key) {
        let source = this.storage.getItem(this.keyPrefix + key);

        if (!source) {
            return null;
        }

        let item = JSON.parse(source);
        let now  = new Date().getTime();

        if (item.expire - now <= 0) {
            return null;
        }

        return item;
    };

    this.call = function (key, expiration, fetchCallback, fetchedCallback) {
        let item = this.get(key);

        if (!item) {
            this._fetch(key, expiration, fetchCallback, fetchedCallback);
        } else {
            fetchedCallback(item.value);
        }
    };

    this.set = function (key, data, expiration) {
        this.storage.setItem(this.keyPrefix + key, JSON.stringify({
            value: data,
            expire: new Date().getTime() + expiration
        }));
    };

    this.remove = function (key) {
        this.storage.removeItem(this.keyPrefix + key);
    };

    this._fetch = function (key, expiration, fetchCallback, fetchedCallback) {
        let self = this;
        let fetched = function (data) {
            self.set(key, data, expiration);
            fetchedCallback(data);
        };

        fetchCallback(fetched);
    }
};

Tulia.Filemanager.Cache.MINUTE      = 1000 * 60;
Tulia.Filemanager.Cache.TEN_MINUTES = 1000 * 60 * 10;
Tulia.Filemanager.Cache.HALF_HOUR   = 1000 * 60 * 30;
Tulia.Filemanager.Cache.HOUR        = 1000 * 60 * 60;
