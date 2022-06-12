import { getWPLocalizedText } from "../utils/functions";
import { WP_LOCALIZED_TEXT } from "../utils/enums";

const $ = jQuery;

export async function getAPITotalAmount() {
    const url = window.streampay.ajax_url;
    const security = window.streampay.security;
    const action = window.streampay.get_total_order;
    const data = { action, security };

    return new Promise((resolve, reject) => {
        $.ajax({
            url,
            type: "POST",
            data,
            success: (res) => {
                if (res && res.data && res.data.total !== undefined) {
                    resolve(+res.data.total);
                } else {
                    reject(getWPLocalizedText(WP_LOCALIZED_TEXT.ORDER_AMOUNT_ERROR));
                }
            },
            error: (err) => {
                reject(getWPLocalizedText(WP_LOCALIZED_TEXT.ORDER_AMOUNT_ERROR));
            },
        });
    });
}