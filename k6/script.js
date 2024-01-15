import http from "k6/http";
import { sleep } from "k6";

// リクエスト回数
let requestCount = 1;

export const options = {
  vus: 2,
  duration: "10s",
};

export default function () {
  const postData = {
    name: `ユーザー${requestCount}`,
  };
  http.post("http://host.docker.internal:8080/api/accounts", postData);
  // リクエスト回数をインクリメント
  requestCount++;
  sleep(1);
}
