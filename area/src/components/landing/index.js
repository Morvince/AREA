import React from 'react'
import { Rect, FirstWave, SecondWave, Text, Button_with_bg, Button_without_bg} from './LandingElements';
import { Icon } from '@iconify/react';
import { useAddAutomation } from '../../api/apiServicesPage';
import { useNavigate } from 'react-router-dom';
import image from './landing.png'

const Landing = () => {
    const navigate = useNavigate();

    const tmpAutomation = useAddAutomation();

    function redirect() {
        tmpAutomation.mutate();
    }

    if (tmpAutomation.isSuccess) {
        navigate("/home", { replace: true, state: { automationId: tmpAutomation.data.data } })
    }

    return (
        <>
            {/* navebar :  */}
            <Text lineheight="1.2" fontsize="35px" top="2%" left="15%" color="black" fontweight="bold" > Hapilink </Text>
            <Button_without_bg to="/doc" top="1.5%" left="45%" height="50px" width="180px"> Documentation </Button_without_bg>
            <Button_without_bg to="/login" top="1.5%" left="80%" height="50px" width="110px"> Log In </Button_without_bg>
            <Button_with_bg to="/sign" top="1.5%" left="88%" height="50px" width="190px"> New Account </Button_with_bg>

            {/* first part  */}
            <Rect top="80px" height="10000px" color="#373b48" Rect />
            <img src={image} alt="image" style={{ position: 'absolute', top: '110px' }} />
            <Text lineheight="1.2" fontsize="60px" top="30%" left="32%"
                color="white" fontweight="bold" > Make everything works <br></br> Together </Text>
            <Text lineheight="1.2" fontsize="30px" top="52%" left="30%"
                color="white" fontweight="" > Link all your application in order to make your life easier ! </Text>
            <Button_with_bg top="85%" left="42%" height="50px" width="300px" onClick={redirect}>Start Now</Button_with_bg>


            <FirstWave top="950px" height="1000px" color="#D4D3DC" FirstWave />
            <Rect top="1300px" height="1100px" color="#D4D3DC" Rect />
            <SecondWave top="2000px" height="1000px" color="#D4D3DC" SecondWave />


            {/* <Icon icon="logos:microsoft-windows" width="300" style={{ position: 'absolute', left: '400px', height: "2500px" }} />
            <Icon icon="flat-color-icons:android-os" width="500" style={{ position: 'absolute', left: '1100px', height: "2500px" }} />
            <Text lineheight="1.2" fontsize="50px" top="900px" left="580px"
                color="black" fontweight="bold" > Available on windows and Android !  </Text>
            <Text lineheight="1.2" fontsize="40px" top="1600px" left="210px"
                color="black" fontweight="bold" > Use the application where and when you want thanks to our mobile and computer app. </Text> */}

            {/* <Text lineheight="1.2" fontsize="50px" top="2000px" left="400px"
                color="black" fontweight="bold" > Link different services between our SIX applications </Text>
            <Icon icon="skill-icons:discord" width="300" style={{ position: 'absolute', left: '100px', top: "2150px" }} />
            <Icon icon="logos:spotify-icon" width="300" style={{ position: 'absolute', left: '100px', top: "2600px" }} />
            <Icon icon="skill-icons:instagram" width="300" style={{ position: 'absolute', left: '800px', top: "2150px" }} />
            <Icon icon="logos:google-icon" width="300" style={{ position: 'absolute', left: '800px', top: "2600px" }} />
            <Icon icon="skill-icons:twitter" width="300" style={{ position: 'absolute', left: '1500px', top: "2150px" }} />
            <Icon icon="logos:openai-icon" width="300" style={{ position: 'absolute', left: '1500px', top: "2600px" }} />


            <Rect top="3100px" height="1100px" color="#373b48" Rect />
            <Text lineheight="1.2" fontsize="50px" top="3250px" left="400px"
                color="white" fontweight="bold" > Connect apps together and let automations work </Text>
            <Text lineheight="1.2" fontsize="40px" top="3350px" left="600px"
                color="white" fontweight="bold" > There are unlimited ways to connect ! </Text>
            <Text lineheight="1.2" fontsize="40px" top="3700px" left="750px"
                color="white" fontweight="bold" > PUT SCREENSHOT  </Text> */}
            {/* <Button2 to="/home" top="4000px" left="795px" width="250px" >Try it</Button2> */}

        </>
    )
};

export default Landing