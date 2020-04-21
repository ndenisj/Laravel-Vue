export default {
    data() {
        return {};
    },
    methods: {
        async callApi(method, url, dataObj) {
            // Send a POST request
            try {
                return await axios({
                    method: method,
                    url: url,
                    data: dataObj
                });
            } catch (e) {
                return e.response;
            }
        },
        // Notice
        info(description = "", title = "Hey") {
            this.$Notice.info({
                title: title,
                desc: description
            });
        },
        success(description = "", title = "Great") {
            this.$Notice.success({
                title: title,
                desc: description
            });
        },
        warning(description = "", title = "Hey") {
            this.$Notice.warning({
                title: title,
                desc: description
            });
        },
        error(description = "", title = "Oops") {
            this.$Notice.error({
                title: title,
                desc: description
            });
        }
    }
};
