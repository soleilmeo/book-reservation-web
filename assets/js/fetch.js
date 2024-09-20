async function fetchTimeout(resource, options = {}) {
    const {
      timeout = 8000,
      retryLimit = 5,
      retryDelay = 0,
      throwExceptionOnFail = true,
      acceptOnly200 = true,
      breakAt404 = false,
    } = options;
  
    var retries = retryLimit;
    var controller;
    var id;
    var response;
  
    var retriesExceeded = false;
  
    while (retries >= 0) {
      try {
        controller = new AbortController();
        id = setTimeout(() => controller.abort(), timeout);
  
        var failed = false;
        response = await fetch(resource, {
          ...options,
          signal: controller.signal,
        }).then((res) => {
          if (breakAt404 && res.status === 404) {
            retries = -1;
            throw "404 not found encountered with retry bypass allowed, stopped future retry attempts.";
          }
  
          if ((acceptOnly200 && res.status != 200) || (!acceptOnly200 && (res.status < 200 || res.status > 299))) {
            failed = true
          }
          else retries = -1;
          return res;
        });
  
        if (failed) {
          throw `Response not OK, ${response.status} instead`;
        }
      } catch (err) {
        clearTimeout(id);
        id = null;
        if (retries > 0) {
          console.log("Retrying...");
          retries--;
          if (retryDelay > 0) {
            await delayAsync(retryDelay);
          }
        } else {
          retriesExceeded = true;
          break;
        }
      }
    }
    if (id) clearTimeout(id);
    if (throwExceptionOnFail) {
      if (retriesExceeded) throw `Exceeded retry times! Also ${err}`;
    }
    return response;
  }