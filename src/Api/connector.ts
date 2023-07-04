import axios, { AxiosError, AxiosResponse } from "axios";
import { env } from "../env";

axios.defaults.baseURL = `${env.API}/api`;
axios.defaults.headers.post["Content-Type"] = "application/json";

axios.interceptors.response.use(
  (res) => res,
  (error: AxiosError) => {
    // eslint-disable-next-line @typescript-eslint/no-non-null-assertion
    const { data, status } = error.response!;
    switch (status) {
      case 400:
        console.error(data);
        break;

      case 401:
        console.error("/unauthorized");
        break;

      case 404:
        console.error("/not-found");
        break;

      case 500:
        console.error("/server-error");
        break;
    }
    return Promise.reject(error);
  }
);
const responseBody = <T>(response: AxiosResponse<T>) => response.data;

const request = {
  get: <T>(url: string) => axios.get<T>(url).then(responseBody),
  post: <T>(url: string, body: unknown) =>
    axios.post<T>(url, body).then(responseBody),
};

export default request;
