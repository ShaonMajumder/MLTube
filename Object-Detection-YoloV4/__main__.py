#!/usr/bin/python3
import argparse
import json
import cv2 as cv
import os
import sys
from dotenv import load_dotenv

def get_script_dir(script_path):
    return os.path.dirname(os.path.realpath(script_path))

# Load environment variables from .env file
load_dotenv()
script_path = sys.argv[0]
script_dir = get_script_dir(script_path)

dnn_cfg_path = script_dir + os.getenv('DNN_CFG_PATH')
dnn_weights_path = script_dir + os.getenv('DNN_WEIGHTS_PATH')
coco_names_path = script_dir + os.getenv('COCO_NAMES_PATH')

parser = argparse.ArgumentParser()
parser.add_argument("-i", "--input", help = "Input File")
parser.add_argument("-it", "--input_type", help = "Input File")
args = parser.parse_args()
    
net = cv.dnn_DetectionModel(dnn_cfg_path, dnn_weights_path)

def imageArray(input,output, confThreshold = 0.8):
    if isinstance(input, str):
        frame = cv.imread(input)
    else:
        frame = input

    height_v, width_v, color_v = frame.shape
    net.setInputSize(704, 704)
    net.setInputScale(1.0 / 255)
    net.setInputSwapRB (True)

    with open(coco_names_path, 'rt') as f:
        names = f.read().rstrip('\n').split('\n')

    classes, confidences, boxes = net.detect(frame, confThreshold=confThreshold, nmsThreshold=0.4)

    image_array = {}

    if classes is not None and confidences is not None and boxes is not None:
        for classId, confidence, box in zip(classes.flatten(), confidences.flatten(), boxes):
            label = '%.2f' % confidence
            label = '%s: %s' % (names[classId], label) 
            label_name = names[classId]

            labelSize, baseline = cv.getTextSize(label, cv.FONT_HERSHEY_SIMPLEX, 0.5, 1) 
            left, top, width, height = box 
            
            if label_name in image_array:
                old_value = image_array[ label_name ]
                old_value.append( [str(confidence),str(box)] )
                image_array[ label_name ] = old_value
            else:
                image_array[ label_name ] = [ [str(confidence),str(box)] ]

            top = max(top, labelSize[1]) 
            cv.rectangle(frame, box, color=(0, 255, 0), thickness=3) 
            cv.rectangle(frame, (left, top - labelSize[1]), (left + labelSize[0], top + baseline), (255, 255, 255), cv.FILLED) 
            cv.putText(frame, label, (left, top), cv.FONT_HERSHEY_SIMPLEX, 0.5, (0, 0, 0))

        cv.imwrite(output, frame)

    return image_array

def getFrameCount(input):
    vidcap = cv.VideoCapture(input)
    fps = vidcap.get(cv.CAP_PROP_FPS)      # OpenCV2 version 2 used "CV_CAP_PROP_FPS"
    frame_count = int(vidcap.get(cv.CAP_PROP_FRAME_COUNT))
    return frame_count


def videoArray(input,sample_size=5):
    vid_file = input
    frame_count = getFrameCount(vid_file)
    vidcap = cv.VideoCapture(vid_file)
    success,frame = vidcap.read()
    frame_interval = int(frame_count/sample_size)
    frame_no = frame_interval
    vid_array = []
    while frame_no <= frame_count:
        vidcap.set( int(vidcap.get(cv.CAP_PROP_POS_FRAMES)) , frame_no)
        # print("Frame No = ",frame_no)
        susccess, frame = vidcap.read()
        cv.imwrite("io/in.jpg" , frame) 
        # vid_array.append( imageArray('io/in.jpg','io/img.png') )
        vid_array.append( imageArray(frame,'io/img.png') )
        frame_no += frame_interval
    return vid_array
    

if args.input and args.input_type:
    input = args.input
    if args.input_type == "video":
        outputInfo = videoArray(input)
    elif args.input_type == "image":
        outputInfo = imageArray(input,'io/ouput.jpg')

    highest_confidence = {}

    # Iterate through each frame
    for frame in outputInfo:
        for tag, detections in frame.items():
            for confidence, _ in detections:
                confidence_value = float(confidence)
                if tag not in highest_confidence or confidence_value > highest_confidence[tag]:
                    highest_confidence[tag] = confidence_value
    json_object = json.dumps(highest_confidence)
    # json_object = json.dumps(outputInfo, indent = 0, separators=(',', ':'))
    json_object = json_object.replace('\n', '')
    print(json_object)
else:
    print(json.dumps({"error": "No input received!"}))