import { z } from "zod";
import request from "./connector";

const Link = z.object({
  id: z.number().int(),
  origin: z.string().url(),
  count: z.number().int().optional().nullable(),
  user: z.string().optional().nullable(),
});

export type Link = z.infer<typeof Link>;

const shorts = {
  create: (url: string) =>
    request.post<
      { message: "No Url given" } | { message: "Success"; short: string }
    >("/create.php", { url: url }),
  delete: (id: number) =>
    request.post<{ message: "No ID given" } | { message: "Deleted" }>(
      "/delete.php",
      { id: id }
    ),
  getAll: () => request.get<Link[]>("/getAll.php"),
  getOne: (id: number) =>
    request.post<{ message: "No ID given" } | Link>("/getOne.php", { id: id }),
  redirect: (id: number) =>
    request.post<{ message: "No ID given" } | Link>("/getOne.php", { id: id }),
};

export default shorts;
