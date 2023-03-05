# HAPILINK [AREA]

## **Stack [MSPR]**

[![Symfony](https://img.shields.io/badge/Symfony-%3E%3D5.2-brightgreen.svg)](https://symfony.com)
![PHP](https://img.shields.io/badge/PHP-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white)
![ReactJS](https://img.shields.io/badge/ReactJS-%2320232A.svg?style=for-the-badge&logo=react&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-%232496ED.svg?style=for-the-badge&logo=docker&logoColor=white)
![HTML](https://img.shields.io/badge/HTML-%23E44D26.svg?style=for-the-badge&logo=html5&logoColor=white)
![CSS](https://img.shields.io/badge/CSS-%231572B6.svg?style=for-the-badge&logo=css3&logoColor=white)

---

<br />
<p align="center">
  <h1 align="center">HAPILINK</h1>
  <p align="center">
    HAPILINK is a project that aims to be able to connect different services in order to make them interact with each other in the way you want.
    <br />
    <br />
    <a href="#Service Available">Service available</a>
    ·
    <a href="#How to build">How to build</a>
    ·
    <a href="#usage">Usage</a>
    ·
    <a href="#Group">Group</a>
    ·
    <a href="#Contact">Contact</a>
  </p>
</p>

---

## **Project Description**

as mentioned earlier, the goal of this project is to allow our application (web and mobile) HAPILINK to connect certain services to each other in an automated way.

And to allow you to use this connection to be able to automate certain tasks on these different services.

It works the same as IFTTT, but it's not the same graphic interface.

This project needed thoses 3 things to work and be complete :

- An application server
- A web client.
- A mobile client.

Clients works by querying the server to request useful datas.

---

## **Service Available**

<div align="center">
  <img alt="Spotify logo" src="https://cdn.svgporn.com/logos/spotify.svg" width="125" style="margin: 0 140px;">
  <img alt="Discord logo" src="https://cdn.svgporn.com/logos/discord.svg" width="125" style="margin: 0 140px;">
  <img alt="Instagram logo" src="https://cdn.svgporn.com/logos/instagram.svg" width="125" style="margin: 0 140px;">
  <img alt="Twitter logo" src="https://cdn.svgporn.com/logos/twitter.svg" width="60" style="margin: 0 140px;">
  <img alt="OpenAI logo" src="https://cdn.svgporn.com/logos/openai.svg" width="125" style="margin: 0 140px;">
</div>

---

## **How to build**

Follow the next steps to build the project:

1) You need to start docker:  

  ```bash
  systemctl start docker
  ```
2) Build the project with docker:

  ```bash
  docker-compose build
  ```
  
---
## **Start Application**

Follow those steps to start our web client, mobile client and server.

```bash
docker-compose up -d
```

Different ports :

| Port        | Service           |
| ----------- | ----------------- |
| 8080        | Server            |
| 8081        | Client Web        |
| N/A          | Client Mobile     |


http://localhost:8081/client.apk should provide an APK, for the mobile client (Android version).

http://localhost:8080/about.json should answer with the application server.

## **Stop Application**

Run this command to stop our application:

```bash
docker-compose down -v
```

---

## **Group**

<table>
  <tr>
    <td><img src="https://github.com//noaleclaire.png" width="200px" height="200px"><br>Noa Leclaire</td>
    <td><img src="https://github.com/NassimAlaimi.png" width="200px" height="200px"><br>Nassim Alaimi</td>
    <td><img src="https://github.com/Nzoooo.png" width="200px" height="200px"><br>Enzo Laurent</td>
    <td><img src="https://github.com/EnzoBonato.png" width="200px" height="200px"><br>Enzo Bonato</td>
    <td><img src="https://github.com/Morvince.png" width="200px" height="200px"><br>Marvin Verain</td>
  </tr>
</table>

---

## **Contact**

Find us here:

<div align="center">
  <table>
    <thead>
      <tr>
        <th>Authors</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Noa Leclaire &lt;noa.leclaire@epitech.eu&gt;</td>
      </tr>
      <tr>
        <td>Nassim Alaimi &lt;nassim.alaimi@epitech.eu&gt;</td>
      </tr>
      <tr>
        <td>Enzo Laurent &lt;enzo.laurent@epitech.eu&gt;</td>
      </tr>
      <tr>
        <td>Enzo Bonato &lt;enzo.bonato@epitech.eu&gt;</td>
      </tr>
      <tr>
        <td>Marvin Verain &lt;marvin.verain@epitech.eu&gt;</td>
      </tr>
    </tbody>
  </table>
</div>

