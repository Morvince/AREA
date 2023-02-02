import React from 'react'
import { Rect, Text, Button1, Button2 } from './LandingElements';
import { Icon } from '@iconify/react';

const Landing = () => {
    return (
        <>
            <Rect top="0px" height="820px" color="#373b48" Rect/>
            <Text lineheight="1.2" fontsize="80px" top="200px" left="550px" 
            color="white" fontweight="bold" > Make everything works <br></br> Together </Text>
            <Text lineheight="1.2" fontsize="40px" top="450px" left="500px" 
            color="white" fontweight="" > Link all your application in order to make your life easier ! </Text>
            <Text lineheight="1.2" fontsize="45px" top="20px" left="25px" 
            color="white" fontweight="bold" > Hapilink </Text>
            <Button1 to="/sign" top="29px" left="1600px" >Login</Button1>
            <Button1 to="/home" top="29px" left="1750px" >Get Started</Button1>
            <Button2 to="/home" top="600px" left="760px" width="400px" >Start Now</Button2>
            <Rect top="820px" height="1000px" color="#D4D3DC" Rect/>
            <Icon icon="logos:microsoft-windows" width="300" style={{ position: 'absolute', left: '400px', height: "2500px" }} />
            <Icon icon="flat-color-icons:android-os" width="500" style={{ position: 'absolute', left: '1100px', height: "2500px" }} />
            <Text lineheight="1.2" fontsize="50px" top="900px" left="580px" 
            color="black" fontweight="bold" > Available on windows and Android !  </Text>
            <Text lineheight="1.2" fontsize="40px" top="1600px" left="210px" 
            color="black" fontweight="bold" > Use the application where and when you want thanks to our mobile and computer app. </Text>

            <Text lineheight="1.2" fontsize="50px" top="2000px" left="400px" 
            color="black" fontweight="bold" > Link different services between our SIX applications </Text>
            <Icon icon="skill-icons:discord" width="300" style={{ position: 'absolute', left: '100px', top: "2150px" }}/>
            <Icon icon="logos:spotify-icon" width="300" style={{ position: 'absolute', left: '100px', top: "2600px" }}/>
            <Icon icon="skill-icons:instagram" width="300" style={{ position: 'absolute', left: '800px', top: "2150px" }}/>
            <Icon icon="logos:google-icon" width="300" style={{ position: 'absolute', left: '800px', top: "2600px" }}/>
            <Icon icon="skill-icons:twitter" width="300" style={{ position: 'absolute', left: '1500px', top: "2150px" }}/>
            <Icon icon="logos:openai-icon" width="300" style={{ position: 'absolute', left: '1500px', top: "2600px" }}/>


            <Rect top="3100px" height="1100px" color="#373b48" Rect/>
            <Text lineheight="1.2" fontsize="50px" top="3250px" left="400px" 
            color="white" fontweight="bold" > Connect apps together and let automations work </Text>
            <Text lineheight="1.2" fontsize="40px" top="3350px" left="600px"
            color="white" fontweight="bold" > There are unlimited ways to connect ! </Text>
            <Text lineheight="1.2" fontsize="40px" top="3700px" left="750px"
            color="white" fontweight="bold" > PUT SCREENSHOT  </Text>
            <Button2 to="/home" top="4000px" left="795px" width="250px" >Try it</Button2>

        </>
    )
};

export default Landing