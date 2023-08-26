import route from 'ziggy-js';

const Ziggy = {
    "url": "http:\/\/127.0.0.1", "port": null, "defaults": {}, "routes": {
        "debugbar.openhandler": {"uri": "_debugbar\/open", "methods": ["GET", "HEAD"]},
        "debugbar.clockwork": {"uri": "_debugbar\/clockwork\/{id}", "methods": ["GET", "HEAD"]},
        "debugbar.assets.css": {"uri": "_debugbar\/assets\/stylesheets", "methods": ["GET", "HEAD"]},
        "debugbar.assets.js": {"uri": "_debugbar\/assets\/javascript", "methods": ["GET", "HEAD"]},
        "debugbar.cache.delete": {"uri": "_debugbar\/cache\/{key}\/{tags?}", "methods": ["DELETE"]},
        "sanctum.csrf-cookie": {"uri": "sanctum\/csrf-cookie", "methods": ["GET", "HEAD"]},
        "ignition.healthCheck": {"uri": "_ignition\/health-check", "methods": ["GET", "HEAD"]},
        "ignition.executeSolution": {"uri": "_ignition\/execute-solution", "methods": ["POST"]},
        "ignition.updateConfig": {"uri": "_ignition\/update-config", "methods": ["POST"]},
        "api.ziggy": {"uri": "api\/ziggy", "methods": ["GET", "HEAD"]},
        "api.user.update": {"uri": "api\/user", "methods": ["POST"]},
        "api.user.resend_confirmation": {"uri": "api\/user\/resend_confirmation", "methods": ["POST"]},
        "api.user.push_avatar": {"uri": "api\/user\/avatar", "methods": ["POST"]},
        "main": {"uri": "\/", "methods": ["GET", "HEAD"]},
        "register": {"uri": "auth\/register", "methods": ["GET", "HEAD"]},
        "login": {"uri": "auth", "methods": ["GET", "HEAD"]},
        "cabinet": {"uri": "auth\/cabinet", "methods": ["GET", "HEAD"]},
        "logout": {"uri": "auth\/logout", "methods": ["GET", "HEAD"]},
        "verify": {"uri": "auth\/verify", "methods": ["GET", "HEAD"]},
        "profile": {"uri": "profile\/{user}", "methods": ["GET", "HEAD"]},
        "languages": {"uri": "languages", "methods": ["GET", "HEAD"]},
        "languages.view": {"uri": "languages\/{code}", "methods": ["GET", "HEAD"]},
        "languages.store": {"uri": "languages", "methods": ["POST"]}
    }
};

if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
    Object.assign(Ziggy.routes, window.Ziggy.routes);
}

const route_ = (name, params, absolute, config = Ziggy) => route(name, params, absolute, config);

export { Ziggy, route_ };
