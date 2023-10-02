<?php

namespace TechStudio\Core\app\Helper; 


class PageContent {
    public function __construct($blocks) {
        $this->blocks = $blocks;
        $this->analyse();  // maybe cache the results??
    }

    private function analyse() {
        $this->estimatedTotalTime = 0;
        $this->minutesToRead = 0;
        $this->videosDuration = 0;
        $this->videosCount = 0;
        $this->questionsCount = 0;
        $this->dominantType = 'text';

        return 'just go';
        // amirmahdi should be fix that!!!!!
        foreach ($this->blocks as $block) {
            if ($block['type'] == 'html') {
                $t = HtmlContent::minutesToRead(json_encode($block['content']));
                $this->minutesToRead += $t;
                $this->estimatedTotalTime += $t;
            } else if ($block['type'] == 'video') {
                $t = $obj['duration'] / 60;
                $this->videosDuration += $t;
                $this->estimatedTotalTime += $t;
                $this->videosCount += 1;
                if ($this->dominantType == 'html') {
                    $this->dominantType = 'video';
                }
            } else if ($block['type'] == 'quiz') {
                $this->dominantType = 'quiz';
                // not handling time for now
            }
        }

        $this->estimatedTotalTime = round($this->estimatedTotalTime);
        $this->minutesToRead = round($this->minutesToRead);
        $this->videosDuration = round($this->videosDuration);
    }

    public function getDominantType() {
        return $this->dominantType;
    }

    public function getEstimatedTotalTime() {
        return $this->estimatedTotalTime;
    }

    public function getMinutesToRead() {
        return $this->minutesToRead;
    }

    public function getVideosDuration() {
        return $this->videosDuration;
    }

    public function getVideosCount() {
        return $this->videosCount;
    }

    public function getQuestionsCount() {
        return $this->questionsCount;
    }
}
