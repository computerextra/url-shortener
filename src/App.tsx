import { useEffect, useState } from "react";
import {
  Button,
  Container,
  FloatingLabel,
  Form,
  FormControl,
  InputGroup,
  Row,
  Spinner,
} from "react-bootstrap";
import { Route, Routes, useNavigate, useParams } from "react-router-dom";
import { api } from "./Api";
import { Link } from "./Api/ShortsRouter";

function App() {
  return (
    <Container fluid>
      <Routes>
        <Route
          path="/"
          element={<Home />}
        />
        <Route
          path="/:id"
          element={<Redirect />}
        />
        <Route
          path="/404"
          element={<NotFound />}
        />
      </Routes>
    </Container>
  );
}

export default App;

function Home() {
  const [url, setUrl] = useState<string>("");
  const [short, setShort] = useState<string | null>(null);

  const handleSubmit = () => {
    async function f() {
      setShort(null);
      if (url == null || url.length < 1) return;
      const res = await api.shorts.create(url);
      if (res == null || res.message === "No Url given") return;
      setShort(res.short);
    }
    f();
  };

  const handleCopy = () => {
    if (short == null) return;
    navigator.clipboard.writeText(window.location.href + short);
    alert("Copied Shortened URL to Clipboard.");
  };

  return (
    <>
      <h1 className="text-center mt-5 mb-5">Computer Extra - URL Shortener</h1>
      <Container
        fluid="sm"
        className="mt-5">
        <Form onSubmit={(e) => e.preventDefault()}>
          <InputGroup>
            <FloatingLabel label="URL">
              <FormControl
                required
                placeholder="URL"
                value={url}
                onChange={(e) => setUrl(e.target.value)}
              />
            </FloatingLabel>
            <Button
              variant="outline-success"
              onClick={handleSubmit}>
              Generieren
            </Button>
          </InputGroup>
        </Form>
      </Container>
      <Container
        fluid="sm"
        className="mt-5 d-flex justify-content-center">
        {short != null && (
          <>
            <a
              href={short}
              target="_blank"
              rel="noopener noreferrer">
              {short}
            </a>
            &nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;
            <Button
              size="sm"
              variant="outline-secondary"
              onClick={handleCopy}>
              COPY
            </Button>
          </>
        )}
      </Container>
    </>
  );
}

// Countdown in Seconds
const countdown = 5;

function Redirect() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [loading, setLoading] = useState(true);
  const [counter, setCounter] = useState(countdown);
  const [short, setShort] = useState<Link | null>(null);

  useEffect(() => {
    async function f() {
      if (
        id == null ||
        id.length <= 0 ||
        id === "" ||
        id == undefined ||
        id === "undefined"
      ) {
        console.log("Ich hab keine id");
        navigate("/404");
        return;
      }

      const res = await api.shorts.redirect(id);
      if (res.message === "No ID given") {
        navigate("/404");
        return;
      }

      setShort(res.short);
      setLoading(false);
    }
    f();
  }, [id]);

  useEffect(() => {
    if (loading) return;
    if (short == null) return;

    counter > 0 && setTimeout(() => setCounter(counter - 1), 1000);

    counter <= 0 && location.replace(short.origin);
  }, [loading, counter, short]);

  if (loading)
    return (
      <Container
        fluid="sm"
        className="mt-5">
        <h1 className="text-center">Loading ...</h1>
      </Container>
    );

  return (
    <Container
      fluid="sm"
      className="mt-5 text-center">
      <Spinner style={{ height: "10rem", width: "10rem" }} />
      <h2 className="text-center mt-5">
        You will be redirected to: <br />
        <a href={short?.origin}>{short?.origin}</a>
        <br />
        in{" "}
        <span className={counter < 3 ? "text-danger fw-bold" : ""}>
          {counter}
        </span>{" "}
        seconds.
      </h2>
    </Container>
  );
}

function NotFound() {
  const navigate = useNavigate();

  return (
    <Container
      fluid="sm"
      className="mt-5">
      <h1 className="text-center">No shortened URL found.</h1>
      <Row>
        <Button
          variant="outline-secondary"
          className="mt-5"
          onClick={() => navigate("/")}>
          Back to start
        </Button>
      </Row>
    </Container>
  );
}
